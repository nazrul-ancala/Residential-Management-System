<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VisitorController extends Controller
{
    /**
     * GET /api/Visitor/GET_VisitorList
     * Admin: all visitors across all residents.
     */
    public function GET_VisitorList()
    {
        try {
            $rows = $this->callCrudAll('GET_ALL', [null, null, null, null, null, null, null, null, null]);
            return $this->ok($rows, 'Visitor list retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_VisitorList Error', ['message' => $ex->getMessage()]);
            return $this->fail('An error occurred while retrieving visitors.', 500, $ex->getMessage());
        }
    }

    /**
     * GET /api/Visitor/GET_MyVisitorList?user_id=...
     * Resident: their own registered visitors.
     */
    public function GET_MyVisitorList(Request $request)
    {
        $userId = $request->input('user_id');
        if (! $userId) {
            return $this->fail('user_id parameter is required.', 400);
        }

        try {
            $rows = $this->callCrudAll('GET_BY_RESIDENT', [null, $userId, null, null, null, null, null, null, null]);
            return $this->ok($rows, 'Visitor list retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_MyVisitorList Error', ['message' => $ex->getMessage(), 'user_id' => $userId]);
            return $this->fail('An error occurred while retrieving visitors.', 500, $ex->getMessage());
        }
    }

    /**
     * GET /api/Visitor/GET_TodayVisitors
     * Security / Admin: today's expected visitors with check-in/out times.
     */
    public function GET_TodayVisitors()
    {
        try {
            $rows = $this->callCrudAll('GET_TODAY', [null, null, null, null, null, null, null, null, null]);
            return $this->ok($rows, 'Today\'s visitors retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_TodayVisitors Error', ['message' => $ex->getMessage()]);
            return $this->fail('An error occurred while retrieving today\'s visitors.', 500, $ex->getMessage());
        }
    }

    /**
     * POST /api/Visitor/POST_Visitor_Save
     * Dispatches on `action`: save | checkin | checkout | cancel | delete
     */
    public function POST_Visitor_Save(Request $request)
    {
        $action = $request->input('action');
        if (! $action) {
            return $this->fail('action parameter is required.', 400);
        }

        try {
            switch (strtolower($action)) {
                case 'save':     return $this->saveVisitor($request);
                case 'checkin':  return $this->checkIn($request);
                case 'checkout': return $this->checkOut($request);
                case 'cancel':   return $this->cancelVisitor($request);
                case 'delete':   return $this->deleteVisitor($request);
                default:
                    return $this->fail('Invalid action. Use: save, checkin, checkout, cancel, or delete.', 400);
            }
        } catch (\Exception $ex) {
            Log::error('POST_Visitor_Save Error', ['message' => $ex->getMessage(), 'action' => $action]);
            return $this->fail('An error occurred while processing the request.', 500, $ex->getMessage());
        }
    }

    private function saveVisitor(Request $request)
    {
        $residentId = $request->input('resident_id');
        $name       = $request->input('visitor_name');
        $ic         = $request->input('ic_passport');

        if (! $residentId || ! $name || ! $ic) {
            return $this->fail('resident_id, visitor_name, and ic_passport are required.', 400);
        }

        $response = $this->callCrud('INSERT', [
            null,
            $residentId,
            $name,
            $ic,
            $request->input('vehicle_number'),
            $request->input('visit_date'),
            $request->input('visit_time'),
            $request->input('purpose'),
            null,
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $response->NewId, 'qr_code' => $response->QrCode ?? null], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to register visitor.', 400);
    }

    private function checkIn(Request $request)
    {
        $id = $request->input('id');
        $checkedBy = $request->input('checked_by');

        if (! $id) {
            return $this->fail('id is required for check-in.', 400);
        }

        $response = $this->callCrud('CHECK_IN', [
            $id, null, null, null, null, null, null, null, $checkedBy,
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to check in visitor.', 400);
    }

    private function checkOut(Request $request)
    {
        $id = $request->input('id');
        $checkedBy = $request->input('checked_by');

        if (! $id) {
            return $this->fail('id is required for check-out.', 400);
        }

        $response = $this->callCrud('CHECK_OUT', [
            $id, null, null, null, null, null, null, null, $checkedBy,
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to check out visitor.', 400);
    }

    private function cancelVisitor(Request $request)
    {
        $id = $request->input('id');
        if (! $id) {
            return $this->fail('id is required for cancel.', 400);
        }

        $response = $this->callCrud('UPDATE_STATUS', [
            $id, null, null, null, null, null, null, 'cancelled', null,
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to cancel visitor.', 400);
    }

    private function deleteVisitor(Request $request)
    {
        $id = $request->input('id');
        if (! $id) {
            return $this->fail('id is required for delete.', 400);
        }

        $response = $this->callCrud('DELETE', [
            $id, null, null, null, null, null, null, null, null,
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Visitor not found.', 404);
    }

    /**
     * POST /api/Visitor/AUTO_Checkout
     * Called by the nightly scheduler — auto-completes any visitors still
     * checked-in from previous days and returns affected rows for notification.
     */
    public function AUTO_Checkout()
    {
        try {
            $pdo  = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_AutoCheckout_Visitors()');
            $stmt->execute();
            $affected = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return $this->ok($affected, count($affected) . ' visitor(s) auto-checked out.');
        } catch (\Exception $ex) {
            Log::error('AUTO_Checkout Error', ['message' => $ex->getMessage()]);
            return $this->fail('An error occurred during auto-checkout.', 500, $ex->getMessage());
        }
    }

    private function callCrud(string $action, array $params)
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Visitor_CRUD(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    private function callCrudAll(string $action, array $params)
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Visitor_CRUD(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
