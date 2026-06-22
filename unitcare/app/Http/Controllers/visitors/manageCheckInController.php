<?php

namespace App\Http\Controllers\visitors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class manageCheckInController extends Controller
{
    public function index()
    {
        return view('visitors.checkIn');
    }

    public function search(Request $request)
    {
        $q      = $request->input('q', '');
        $apiUrl = config('api.url') . '/api/Visitor/GET_TodayVisitors';

        try {
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withoutVerifying()
                ->get($apiUrl);

            $data = $response->json();
            $rows = ($data['Success'] ?? false) ? ($data['Data'] ?? []) : [];

            if ($q) {
                $q    = strtolower($q);
                $rows = array_values(array_filter($rows, function ($r) use ($q) {
                    return str_contains(strtolower($r['name'] ?? ''), $q)
                        || str_contains(strtolower($r['ic_passport'] ?? ''), $q)
                        || str_contains(strtolower($r['qr_code'] ?? ''), $q);
                }));
            }

            return response()->json(['data' => $rows]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function processCheckIn(Request $request)
    {
        $apiUrl = config('api.url') . '/api/Visitor/POST_Visitor_Save';

        try {
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->post($apiUrl, [
                    'action'     => $request->input('action'),
                    'id'         => $request->input('id'),
                    'checked_by' => Auth::id(),
                ]);

            $data = $response->json();

            return response()->json([
                'status'  => $data['Success'] ?? false,
                'message' => $data['Message'] ?? 'Operation completed.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
