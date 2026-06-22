<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestController extends Controller
{
    /**
     * GET /api/Request/GET_RequestList
     * Admin list: all maintenance requests with resident / unit / block info.
     */
    public function GET_RequestList(Request $request)
    {
        try {
            $requests = $this->callCrudAll('GET_ALL', [null, null, null, null, null, null, null, null, null]);

            return $this->ok($requests, 'Request data retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_RequestList Error', [
                'message' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString(),
            ]);

            return $this->fail('An error occurred while retrieving requests.', 500, $ex->getMessage());
        }
    }

    /**
     * GET /api/Request/GET_MyRequestList?user_id=...
     * Resident list: requests belonging to the given user's resident profile.
     */
    public function GET_MyRequestList(Request $request)
    {
        $userId = $request->input('user_id');

        if (! $userId) {
            return $this->fail('user_id parameter is required.', 400);
        }

        try {
            $requests = $this->callCrudAll('GET_BY_USER', [null, $userId, null, null, null, null, null, null, null]);

            return $this->ok($requests, 'Request data retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_MyRequestList Error', [
                'message' => $ex->getMessage(),
                'user_id' => $userId,
            ]);

            return $this->fail('An error occurred while retrieving requests.', 500, $ex->getMessage());
        }
    }

    /**
     * GET /api/Request/GET_SpecificRequest?id=...
     */
    public function GET_SpecificRequest(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('id parameter is required.', 400);
        }

        try {
            $ticket = $this->callCrud('GET_BY_ID', [$id, null, null, null, null, null, null, null, null]);

            if (! $ticket) {
                return $this->fail('Request not found.', 404);
            }

            return $this->ok($ticket, 'Request data retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_SpecificRequest Error', [
                'message' => $ex->getMessage(),
                'id' => $id,
            ]);

            return $this->fail('An error occurred while retrieving the request.', 500, $ex->getMessage());
        }
    }

    /**
     * POST /api/Request/POST_Request_SaveUpdateDelete
     * Dispatches on the `action` field: save | update | delete.
     */
    public function POST_Request_SaveUpdateDelete(Request $request)
    {
        $action = $request->input('action');

        if (! $action) {
            return $this->fail('action parameter is required.', 400);
        }

        try {
            switch (strtolower($action)) {
                case 'save':
                    return $this->saveRequest($request);
                case 'update':
                    return $this->updateRequest($request);
                case 'delete':
                    return $this->deleteRequest($request);
                default:
                    return $this->fail('Invalid action. Use: save, update, or delete.', 400);
            }
        } catch (\Exception $ex) {
            Log::error('POST_Request_SaveUpdateDelete Error', [
                'message' => $ex->getMessage(),
                'action' => $action,
            ]);

            return $this->fail('An error occurred while processing your request.', 500, $ex->getMessage());
        }
    }

    private function saveRequest(Request $request)
    {
        $userId = $request->input('user_id');
        $category = $request->input('category');
        $priority = $request->input('priority');
        $description = $request->input('description');

        if (! $userId || ! $category || ! $priority || ! $description) {
            return $this->fail('user_id, category, priority and description are required.', 400);
        }

        $response = $this->callCrud('INSERT', [
            null,
            $userId,
            $category,
            $priority,
            $request->input('title', $category.' Request'),
            $description,
            $request->input('attachment_path'),
            'open',
            null,
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $response->NewId ?? null], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to submit request.', 400);
    }

    private function updateRequest(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('id is required for update.', 400);
        }

        $response = $this->callCrud('UPDATE', [
            $id,
            null,
            $request->input('category'),
            $request->input('priority'),
            $request->input('title'),
            $request->input('description'),
            $request->input('attachment_path'),
            $request->input('status'),
            $request->input('assigned_to'),
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to update request.', 400);
    }

    private function deleteRequest(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('id is required for delete.', 400);
        }

        $response = $this->callCrud('DELETE', [$id, null, null, null, null, null, null, null, null]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Request not found.', 404);
    }

    /**
     * GET /api/Request/GET_AssignedRequestList?user_id=...
     * Technician list: tickets assigned to the given user.
     */
    public function GET_AssignedRequestList(Request $request)
    {
        $userId = $request->input('user_id');

        if (! $userId) {
            return $this->fail('user_id parameter is required.', 400);
        }

        try {
            $rows = $this->callCrudAll('GET_BY_ASSIGNED', [null, $userId, null, null, null, null, null, null, null]);

            return $this->ok($rows, 'Assigned requests retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_AssignedRequestList Error', [
                'message' => $ex->getMessage(),
                'user_id' => $userId,
            ]);

            return $this->fail('An error occurred while retrieving assigned requests.', 500, $ex->getMessage());
        }
    }

    /**
     * Execute sp_Request_CRUD with the given action and the 9 trailing params
     * (Id, UserId, Category, Priority, Title, Description, AttachmentPath,
     * Status, AssignedTo) and return a single row.
     */
    private function callCrud(string $action, array $params)
    {
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Request_CRUD(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));

        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    /**
     * Same as callCrud but returns all rows (list actions).
     */
    private function callCrudAll(string $action, array $params)
    {
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Request_CRUD(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
