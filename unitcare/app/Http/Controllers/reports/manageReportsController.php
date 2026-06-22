<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class manageReportsController extends Controller
{
    private function apiUrl(string $path): string
    {
        return config('api.url') . '/api/' . $path;
    }

    private function apiGet(string $path, array $query = [])
    {
        return Http::timeout(60)
            ->withBasicAuth(config('api.pass1'), config('api.pass2'))
            ->withoutVerifying()
            ->get($this->apiUrl($path), $query);
    }

    public function index()
    {
        return view('reports.manageReports');
    }

    public function getReportSummary(Request $request)
    {
        try {
            $res = $this->apiGet('Report/GET_ReportSummary', [
                'report_type' => $request->query('report_type', 'visitor'),
                'date_from'   => $request->query('date_from'),
                'date_to'     => $request->query('date_to'),
            ])->json();
            return response()->json([
                'status' => $res['Success']  ?? false,
                'data'   => $res['Data']     ?? null,
                'message'=> $res['Message']  ?? '',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function getReportData(Request $request)
    {
        try {
            $res = $this->apiGet('Report/GET_ReportData', [
                'report_type' => $request->query('report_type', 'visitor'),
                'date_from'   => $request->query('date_from'),
                'date_to'     => $request->query('date_to'),
            ])->json();
            return response()->json([
                'data' => ($res['Success'] ?? false) ? ($res['Data'] ?? []) : [],
            ]);
        } catch (\Exception $e) {
            return response()->json(['data' => []]);
        }
    }
}
