<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class ResidentController extends Controller
{
    public function GET_ResidentList(): JsonResponse
    {
        try {
            $pdo  = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_Resident_CRUD(?,?,?,?,?,?,?,?,?)');
            $stmt->execute(['GET_ALL', null, null, null, null, null, null, null, null]);
            return $this->ok($stmt->fetchAll(PDO::FETCH_OBJ));
        } catch (\Throwable $e) {
            Log::error('GET_ResidentList', ['error' => $e->getMessage()]);
            return $this->fail('Failed to retrieve residents.', 500, $e->getMessage());
        }
    }

    public function POST_Resident_SaveUpdateDelete(Request $request): JsonResponse
    {
        $action = strtolower($request->input('action', ''));

        try {
            return match ($action) {
                'save'   => $this->saveResident($request),
                'update' => $this->updateResident($request),
                'delete' => $this->deleteResident($request),
                default  => $this->fail('Invalid action.'),
            };
        } catch (\Throwable $e) {
            Log::error('POST_Resident_SaveUpdateDelete', ['action' => $action, 'error' => $e->getMessage()]);
            return $this->fail('Operation failed.', 500, $e->getMessage());
        }
    }

    private function saveResident(Request $request): JsonResponse
    {
        $hashed   = password_hash('unitcare@123', PASSWORD_BCRYPT);
        $response = $this->callCrud('INSERT', [
            null,
            $request->input('name'),
            $request->input('email'),
            $hashed,
            $request->input('phone'),
            $request->input('block'),
            $request->input('unit'),
            $request->input('status', 'active'),
        ]);
        return $this->writeResponse($response);
    }

    private function updateResident(Request $request): JsonResponse
    {
        $response = $this->callCrud('UPDATE', [
            $request->input('id'),
            $request->input('name'),
            $request->input('email'),
            null,
            $request->input('phone'),
            $request->input('block') ?: null,
            $request->input('unit') ?: null,
            $request->input('status'),
        ]);
        return $this->writeResponse($response);
    }

    private function deleteResident(Request $request): JsonResponse
    {
        $response = $this->callCrud('DELETE', [
            $request->input('id'),
            null, null, null, null, null, null, null,
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
        $stmt = $pdo->prepare('CALL sp_Resident_CRUD(?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$action, ...$params]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
