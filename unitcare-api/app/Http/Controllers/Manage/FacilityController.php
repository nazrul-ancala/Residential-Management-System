<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacilityController extends Controller
{
    /**
     * GET /api/Facility/GET_FacilityList
     */
    public function GET_FacilityList(Request $request)
    {
        try {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_Facility_CRUD(?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                'GET_ALL',  // Action
                null,       // Id
                null,       // Name
                null,       // Description
                null,       // MaxBookingHours
                null,       // Capacity
                null,       // Status
            ]);

            $facilities = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return $this->ok($facilities, 'Facility data retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_FacilityList Error', [
                'message' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString(),
            ]);

            return $this->fail('An error occurred while retrieving facilities.', 500, $ex->getMessage());
        }
    }

    /**
     * GET /api/Facility/GET_SpecificFacility?id=...
     */
    public function GET_SpecificFacility(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('id parameter is required.', 400);
        }

        try {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_Facility_CRUD(?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute(['GET_BY_ID', $id, null, null, null, null, null]);

            $facility = $stmt->fetch(\PDO::FETCH_OBJ);

            if (! $facility) {
                return $this->fail('Facility not found.', 404);
            }

            return $this->ok($facility, 'Facility data retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_SpecificFacility Error', [
                'message' => $ex->getMessage(),
                'id' => $id,
            ]);

            return $this->fail('An error occurred while retrieving the facility.', 500, $ex->getMessage());
        }
    }

    /**
     * POST /api/Facility/POST_Facility_SaveUpdateDelete
     * Dispatches on the `action` field: save | update | delete.
     */
    public function POST_Facility_SaveUpdateDelete(Request $request)
    {
        $action = $request->input('action');

        if (! $action) {
            return $this->fail('action parameter is required.', 400);
        }

        try {
            switch (strtolower($action)) {
                case 'save':
                    return $this->saveFacility($request);
                case 'update':
                    return $this->updateFacility($request);
                case 'delete':
                    return $this->deleteFacility($request);
                default:
                    return $this->fail('Invalid action. Use: save, update, or delete.', 400);
            }
        } catch (\Exception $ex) {
            Log::error('POST_Facility_SaveUpdateDelete Error', [
                'message' => $ex->getMessage(),
                'action' => $action,
            ]);

            return $this->fail('An error occurred while processing your request.', 500, $ex->getMessage());
        }
    }

    private function saveFacility(Request $request)
    {
        $name = $request->input('name');

        if (! $name) {
            return $this->fail('name is required.', 400);
        }

        $response = $this->callCrud('INSERT', [
            null,
            $name,
            $request->input('description'),
            $request->input('max_booking_hours'),
            $request->input('capacity'),
            $request->input('status', 'available'),
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $response->NewId ?? null], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to save facility.', 400);
    }

    private function updateFacility(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('id is required for update.', 400);
        }

        $response = $this->callCrud('UPDATE', [
            $id,
            $request->input('name'),
            $request->input('description'),
            $request->input('max_booking_hours'),
            $request->input('capacity'),
            $request->input('status'),
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to update facility.', 400);
    }

    private function deleteFacility(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('id is required for delete.', 400);
        }

        $response = $this->callCrud('DELETE', [$id, null, null, null, null, null]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Facility not found.', 404);
    }

    /**
     * Execute sp_Facility_CRUD with the given action and the 6 trailing params
     * (Id, Name, Description, MaxBookingHours, Capacity, Status) and return the row.
     */
    private function callCrud(string $action, array $params)
    {
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Facility_CRUD(?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));

        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
}
