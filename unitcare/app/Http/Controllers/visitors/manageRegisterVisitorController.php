<?php

namespace App\Http\Controllers\visitors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class manageRegisterVisitorController extends Controller
{
    public function index()
    {
        return view('visitors.registerVisitor');
    }

    public function store(Request $request)
    {
        $apiUrl = config('api.url') . '/api/Visitor/POST_Visitor_Save';

        $residentId = Auth::user()->role === 'admin'
            ? $request->input('resident_id')
            : Auth::id();

        try {
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->post($apiUrl, [
                    'action'         => 'save',
                    'resident_id'    => $residentId,
                    'visitor_name'   => $request->input('visitor_name'),
                    'ic_passport'    => $request->input('ic_passport'),
                    'vehicle_number' => $request->input('vehicle_number'),
                    'visit_date'     => $request->input('visit_date'),
                    'visit_time'     => $request->input('visit_time'),
                    'purpose'        => $request->input('purpose'),
                ]);

            $data = $response->json();

            return response()->json([
                'status'  => $data['Success'] ?? false,
                'message' => $data['Message'] ?? 'Operation completed.',
                'data'    => $data['Data'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getResidentList()
    {
        $apiUrl = config('api.url') . '/api/Resident/GET_ResidentList';

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
