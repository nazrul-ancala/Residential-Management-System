<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParcelController extends Controller
{
    /**
     * GET /api/Parcel/GET_AllParcels
     * Admin: all parcel records with resident info.
     */
    public function GET_AllParcels()
    {
        try {
            $rows = $this->callCrudAll('GET_ALL', [null, null, null, null, null, null, null, null, null, null]);
            return $this->ok($rows, 'Parcel list retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_AllParcels Error', ['message' => $ex->getMessage()]);
            return $this->fail('An error occurred while retrieving parcels.', 500, $ex->getMessage());
        }
    }

    /**
     * GET /api/Parcel/GET_PendingParcels
     * Security/Admin: pending parcels awaiting collection.
     */
    public function GET_PendingParcels()
    {
        try {
            $rows = $this->callCrudAll('GET_PENDING', [null, null, null, null, null, null, null, null, null, null]);
            return $this->ok($rows, 'Pending parcels retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_PendingParcels Error', ['message' => $ex->getMessage()]);
            return $this->fail('An error occurred while retrieving pending parcels.', 500, $ex->getMessage());
        }
    }

    /**
     * GET /api/Parcel/GET_MyParcels?user_id=...
     * Resident: their own parcels.
     */
    public function GET_MyParcels(Request $request)
    {
        $userId = $request->input('user_id');
        if (! $userId) {
            return $this->fail('user_id parameter is required.', 400);
        }

        try {
            $rows = $this->callCrudAll('GET_BY_RESIDENT', [null, $userId, null, null, null, null, null, null, null, null]);
            return $this->ok($rows, 'Parcel list retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_MyParcels Error', ['message' => $ex->getMessage(), 'user_id' => $userId]);
            return $this->fail('An error occurred while retrieving parcels.', 500, $ex->getMessage());
        }
    }

    /**
     * POST /api/Parcel/POST_Parcel_Save
     * Dispatches on `action`: log | collect | delete
     */
    public function POST_Parcel_Save(Request $request)
    {
        $action = $request->input('action');
        if (! $action) {
            return $this->fail('action parameter is required.', 400);
        }

        try {
            switch (strtolower($action)) {
                case 'log':     return $this->logParcel($request);
                case 'collect': return $this->collectParcel($request);
                case 'delete':  return $this->deleteParcel($request);
                default:
                    return $this->fail('Invalid action. Use: log, collect, or delete.', 400);
            }
        } catch (\Exception $ex) {
            Log::error('POST_Parcel_Save Error', ['message' => $ex->getMessage(), 'action' => $action]);
            return $this->fail('An error occurred while processing the request.', 500, $ex->getMessage());
        }
    }

    private function logParcel(Request $request)
    {
        $residentId = $request->input('resident_id');
        $loggedBy   = $request->input('logged_by');
        $courier    = $request->input('courier');

        if (! $residentId || ! $loggedBy || ! $courier) {
            return $this->fail('resident_id, logged_by, and courier are required.', 400);
        }

        $response = $this->callCrud('INSERT', [
            null,
            $residentId,
            $loggedBy,
            $courier,
            $request->input('parcel_type', 'small_box'),
            $request->input('tracking_no'),
            $request->input('notes'),
            $request->input('photo_path'),
            null,
            null,
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok([
                'id'             => $response->NewId,
                'collection_pin' => $response->collection_pin ?? null,
            ], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to log parcel.', 400);
    }

    private function collectParcel(Request $request)
    {
        $id          = $request->input('id');
        $collectedBy = $request->input('collected_by');
        $pin         = $request->input('pin', '');

        if (! $id) {
            return $this->fail('id is required for collect.', 400);
        }

        $response = $this->callCrud('COLLECT', [
            $id, null, null, null, null, null, null, null, $collectedBy, $pin,
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to mark parcel as collected.', 400);
    }

    private function deleteParcel(Request $request)
    {
        $id = $request->input('id');
        if (! $id) {
            return $this->fail('id is required for delete.', 400);
        }

        $response = $this->callCrud('DELETE', [
            $id, null, null, null, null, null, null, null, null, null,
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Parcel not found.', 404);
    }

    private function callCrud(string $action, array $params)
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Parcel_CRUD(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    private function callCrudAll(string $action, array $params)
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Parcel_CRUD(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
