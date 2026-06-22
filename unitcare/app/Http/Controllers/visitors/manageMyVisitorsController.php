<?php

namespace App\Http\Controllers\visitors;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class manageMyVisitorsController extends Controller
{
    public function index()
    {
        return view('visitors.myVisitors');
    }

    public function getVisitorList()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $apiUrl = config('api.url') . '/api/Visitor/GET_VisitorList';
        } else {
            $apiUrl = config('api.url') . '/api/Visitor/GET_MyVisitorList?user_id=' . $user->id;
        }

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
