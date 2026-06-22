<?php
namespace App\Http\Controllers\visitors;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class manageTodayVisitorsController extends Controller
{
    public function index()
    {
        return view('visitors.todayVisitors');
    }

    public function getTodayList()
    {
        $apiUrl = config('api.url') . '/api/Visitor/GET_TodayVisitors';

        try {
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withoutVerifying()
                ->get($apiUrl);

            $data = $response->json();

            return response()->json(['data' => ($data['Success'] ?? false) ? ($data['Data'] ?? []) : []]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }
}
