<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    /**
     * GET /api/Announcement/GET_AllAnnouncements
     * Admin: all announcements with derived status field.
     */
    public function GET_AllAnnouncements()
    {
        try {
            $rows = $this->callCrudAll('GET_ALL', [null, null, null, null, null, null, null]);
            return $this->ok($rows, 'Announcement list retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_AllAnnouncements Error', ['message' => $ex->getMessage()]);
            return $this->fail('An error occurred while retrieving announcements.', 500, $ex->getMessage());
        }
    }

    /**
     * GET /api/Announcement/GET_PublishedAnnouncements
     * Residents / Security: only live (published_at <= NOW()) announcements.
     */
    public function GET_PublishedAnnouncements()
    {
        try {
            $rows = $this->callCrudAll('GET_PUBLISHED', [null, null, null, null, null, null, null]);
            return $this->ok($rows, 'Published announcements retrieved successfully.');
        } catch (\Exception $ex) {
            Log::error('GET_PublishedAnnouncements Error', ['message' => $ex->getMessage()]);
            return $this->fail('An error occurred while retrieving announcements.', 500, $ex->getMessage());
        }
    }

    /**
     * POST /api/Announcement/POST_Announcement_Save
     * Dispatches on `action`: save | update | delete
     */
    public function POST_Announcement_Save(Request $request)
    {
        $action = $request->input('action');
        if (! $action) {
            return $this->fail('action parameter is required.', 400);
        }

        try {
            switch (strtolower($action)) {
                case 'save':   return $this->saveAnnouncement($request);
                case 'update': return $this->updateAnnouncement($request);
                case 'delete': return $this->deleteAnnouncement($request);
                default:
                    return $this->fail('Invalid action. Use: save, update, or delete.', 400);
            }
        } catch (\Exception $ex) {
            Log::error('POST_Announcement_Save Error', ['message' => $ex->getMessage(), 'action' => $action]);
            return $this->fail('An error occurred while processing the request.', 500, $ex->getMessage());
        }
    }

    private function saveAnnouncement(Request $request)
    {
        $createdBy = $request->input('user_id');
        $title     = $request->input('title');
        $content   = $request->input('content');
        $type      = $request->input('type');

        if (! $createdBy || ! $title || ! $content || ! $type) {
            return $this->fail('user_id, title, content, and type are required.', 400);
        }

        $response = $this->callCrud('INSERT', [
            null,
            $createdBy,
            $title,
            $content,
            $type,
            $request->input('published_at'),
            $request->input('image_path'),
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $response->NewId], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to create announcement.', 400);
    }

    private function updateAnnouncement(Request $request)
    {
        $id = $request->input('id');
        if (! $id) {
            return $this->fail('id is required for update.', 400);
        }

        $response = $this->callCrud('UPDATE', [
            $id,
            null,
            $request->input('title'),
            $request->input('content'),
            $request->input('type'),
            $request->input('published_at'),
            $request->input('image_path'),
        ]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Failed to update announcement.', 400);
    }

    private function deleteAnnouncement(Request $request)
    {
        $id = $request->input('id');
        if (! $id) {
            return $this->fail('id is required for delete.', 400);
        }

        $response = $this->callCrud('DELETE', [$id, null, null, null, null, null, null]);

        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $id], $response->Message);
        }

        return $this->fail($response->Message ?? 'Announcement not found.', 404);
    }

    private function callCrud(string $action, array $params)
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Announcement_CRUD(?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    private function callCrudAll(string $action, array $params)
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Announcement_CRUD(?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
