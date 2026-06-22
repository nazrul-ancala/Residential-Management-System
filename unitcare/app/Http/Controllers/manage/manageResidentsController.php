<?php

namespace App\Http\Controllers\manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class manageResidentsController extends Controller
{
    private function api(string $path): string
    {
        return config('api.url') . $path;
    }

    private function http()
    {
        return Http::timeout(60)
            ->withBasicAuth(config('api.pass1'), config('api.pass2'))
            ->withoutVerifying();
    }

    public function index()
    {
        return view('manage.manageResidents');
    }

    public function getResidentList()
    {
        try {
            $res = $this->http()->get($this->api('/api/Resident/GET_ResidentList'));
            $d   = $res->json();
            return response()->json(['data' => ($d['Success'] ?? false) ? ($d['Data'] ?? []) : []]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function POST_Resident_SaveUpdateDelete(Request $request)
    {
        try {
            $res = $this->http()->post($this->api('/api/Resident/POST_Resident_SaveUpdateDelete'), [
                'action'   => $request->action,
                'id'       => $request->id,
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $request->password,
                'phone'    => $request->phone,
                'block'    => $request->block,
                'unit'     => $request->unit,
                'status'   => $request->status,
            ]);
            $d = $res->json();
            return response()->json(['status' => $d['Success'] ?? false, 'message' => $d['Message'] ?? 'Done.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
