<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class CommentController extends Controller
{
    public function GET_CommentList(Request $request): JsonResponse
    {
        $ticketId = $request->input('ticket_id');

        if (! $ticketId) {
            return $this->fail('ticket_id parameter is required.', 400);
        }

        try {
            $pdo  = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_Comment_CRUD(?,?,?,?,?)');
            $stmt->execute(['GET_BY_TICKET', null, $ticketId, null, null]);
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $this->ok($rows);
        } catch (\Throwable $e) {
            Log::error('GET_CommentList', ['error' => $e->getMessage()]);
            return $this->fail('Failed to retrieve comments.', 500, $e->getMessage());
        }
    }

    public function POST_Comment_Save(Request $request): JsonResponse
    {
        $action = strtolower($request->input('action', 'save'));

        try {
            return match ($action) {
                'save'   => $this->insertComment($request),
                'delete' => $this->deleteComment($request),
                default  => $this->fail('Invalid action. Use: save or delete.', 400),
            };
        } catch (\Throwable $e) {
            Log::error('POST_Comment_Save', ['action' => $action, 'error' => $e->getMessage()]);
            return $this->fail('Operation failed.', 500, $e->getMessage());
        }
    }

    private function insertComment(Request $request): JsonResponse
    {
        $ticketId = $request->input('ticket_id');
        $userId   = $request->input('user_id');
        $comment  = $request->input('comment');

        if (! $ticketId || ! $userId || ! $comment) {
            return $this->fail('ticket_id, user_id, and comment are required.', 400);
        }

        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Comment_CRUD(?,?,?,?,?)');
        $stmt->execute(['INSERT', null, $ticketId, $userId, $comment]);
        $row  = $stmt->fetch(PDO::FETCH_OBJ);

        if ($row && $row->Status === 'true') {
            return $this->ok(['id' => $row->NewId ?? null], $row->Message);
        }

        return $this->fail($row->Message ?? 'Failed to add comment.', 400);
    }

    private function deleteComment(Request $request): JsonResponse
    {
        $id = $request->input('id');

        if (! $id) {
            return $this->fail('id is required for delete.', 400);
        }

        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Comment_CRUD(?,?,?,?,?)');
        $stmt->execute(['DELETE', $id, null, null, null]);
        $row  = $stmt->fetch(PDO::FETCH_OBJ);

        if ($row && $row->Status === 'true') {
            return $this->ok(['id' => $id], $row->Message);
        }

        return $this->fail($row->Message ?? 'Comment not found.', 404);
    }
}
