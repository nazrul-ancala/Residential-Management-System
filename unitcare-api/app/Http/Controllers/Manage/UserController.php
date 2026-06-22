<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class UserController extends Controller
{
    public function GET_UserList(): JsonResponse
    {
        try {
            $pdo  = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_User_CRUD(?,?,?,?,?,?,?,?)');
            $stmt->execute(['GET_ALL', null, null, null, null, null, null, null]);
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $this->ok($rows);
        } catch (\Throwable $e) {
            Log::error('GET_UserList', ['error' => $e->getMessage()]);
            return $this->fail('Failed to retrieve users.', 500, $e->getMessage());
        }
    }

    public function GET_StaffList(): JsonResponse
    {
        try {
            $pdo  = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_User_CRUD(?,?,?,?,?,?,?,?)');
            $stmt->execute(['GET_STAFF', null, null, null, null, null, null, null]);
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $this->ok($rows);
        } catch (\Throwable $e) {
            Log::error('GET_StaffList', ['error' => $e->getMessage()]);
            return $this->fail('Failed to retrieve staff.', 500, $e->getMessage());
        }
    }

    public function GET_SpecificUser(Request $request): JsonResponse
    {
        try {
            $pdo  = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_User_CRUD(?,?,?,?,?,?,?,?)');
            $stmt->execute(['GET_BY_ID', $request->input('id'), null, null, null, null, null, null]);
            $row  = $stmt->fetch(PDO::FETCH_OBJ);
            return $row ? $this->ok($row) : $this->fail('User not found.', 404);
        } catch (\Throwable $e) {
            Log::error('GET_SpecificUser', ['error' => $e->getMessage()]);
            return $this->fail('Failed to retrieve user.', 500, $e->getMessage());
        }
    }

    public function POST_User_SaveUpdateDelete(Request $request): JsonResponse
    {
        $action = strtolower($request->input('action', ''));

        try {
            return match ($action) {
                'save'   => $this->saveUser($request),
                'update' => $this->updateUser($request),
                'delete' => $this->deleteUser($request),
                default  => $this->fail('Invalid action.'),
            };
        } catch (\Throwable $e) {
            Log::error('POST_User_SaveUpdateDelete', ['action' => $action, 'error' => $e->getMessage()]);
            return $this->fail('Operation failed.', 500, $e->getMessage());
        }
    }

    private function saveUser(Request $request): JsonResponse
    {
        $hashed  = password_hash($request->input('password', ''), PASSWORD_BCRYPT);
        $response = $this->callCrud('INSERT', [
            null,
            $request->input('name'),
            $request->input('email'),
            $hashed,
            $request->input('role', 'resident'),
            $request->input('phone'),
            $request->input('status', 'active'),
        ]);
        return $this->writeResponse($response);
    }

    private function updateUser(Request $request): JsonResponse
    {
        $rawPass  = $request->input('password');
        $hashed   = ($rawPass && strlen($rawPass) > 0) ? password_hash($rawPass, PASSWORD_BCRYPT) : null;
        $response = $this->callCrud('UPDATE', [
            $request->input('id'),
            $request->input('name'),
            $request->input('email'),
            $hashed,
            $request->input('role'),
            $request->input('phone'),
            $request->input('status'),
        ]);
        return $this->writeResponse($response);
    }

    private function deleteUser(Request $request): JsonResponse
    {
        $response = $this->callCrud('DELETE', [
            $request->input('id'),
            null, null, null, null, null, null,
        ]);
        return $this->writeResponse($response);
    }

    private function writeResponse(?object $row): JsonResponse
    {
        if (!$row) return $this->fail('No response from stored procedure.', 500);
        $success = isset($row->Status) && $row->Status === 'true';
        return $success
            ? $this->ok(['NewId' => $row->NewId ?? null], $row->Message ?? 'Done.')
            : $this->fail($row->Message ?? 'Operation failed.');
    }

    private function callCrud(string $action, array $params): mixed
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_User_CRUD(?,?,?,?,?,?,?,?)');
        $stmt->execute([$action, ...$params]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
