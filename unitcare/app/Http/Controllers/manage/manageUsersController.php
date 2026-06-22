<?php

namespace App\Http\Controllers\manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class manageUsersController extends Controller
{
    public function index()
    {
        return view('manage.manageUsers');
    }

    public function getUserList()
    {
        $url = config('api.url') . '/api/User/GET_UserList';

        try {
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withoutVerifying()
                ->get($url);

            $apiData = $response->json();

            return response()->json(['data' => ($apiData['Success'] ?? false) ? ($apiData['Data'] ?? []) : []]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function POST_User_SaveUpdateDelete(Request $request)
    {
        $url = config('api.url') . '/api/User/POST_User_SaveUpdateDelete';

        try {
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withoutVerifying()
                ->post($url, [
                    'action'   => $request->action,
                    'id'       => $request->id,
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => $request->password,
                    'role'     => $request->role,
                    'phone'    => $request->phone,
                    'status'   => $request->status,
                ]);

            $apiData = $response->json();

            return response()->json([
                'status'  => $apiData['Success'] ?? false,
                'message' => $apiData['Message'] ?? 'Operation completed.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
