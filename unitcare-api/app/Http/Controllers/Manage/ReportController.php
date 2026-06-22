<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * GET /api/Report/GET_ReportSummary
     * Query params: report_type, date_from, date_to
     */
    public function GET_ReportSummary(Request $request)
    {
        try {
            $row = $this->callCrudRow('GET_SUMMARY', [
                $request->query('report_type', 'visitor'),
                $request->query('date_from'),
                $request->query('date_to'),
            ]);
            return $this->ok($row, 'Summary retrieved.');
        } catch (\Exception $ex) {
            Log::error('GET_ReportSummary Error', ['message' => $ex->getMessage()]);
            return $this->fail('Error retrieving summary.', 500, $ex->getMessage());
        }
    }

    /**
     * GET /api/Report/GET_ReportData
     * Query params: report_type, date_from, date_to
     */
    public function GET_ReportData(Request $request)
    {
        try {
            $rows = $this->callCrudAll('GET_DATA', [
                $request->query('report_type', 'visitor'),
                $request->query('date_from'),
                $request->query('date_to'),
            ]);
            return $this->ok($rows, 'Report data retrieved.');
        } catch (\Exception $ex) {
            Log::error('GET_ReportData Error', ['message' => $ex->getMessage()]);
            return $this->fail('Error retrieving report data.', 500, $ex->getMessage());
        }
    }

    private function callCrudRow(string $action, array $params)
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Report_CRUD(?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    private function callCrudAll(string $action, array $params)
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_Report_CRUD(?, ?, ?, ?)');
        $stmt->execute(array_merge([$action], $params));
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
