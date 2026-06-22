<?php

namespace App\Http\Controllers\parcels;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class manageMyParcelsController extends Controller
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
        return view('parcels.myParcels');
    }

    public function getMyParcelList()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $apiPath = 'Parcel/GET_AllParcels';
            $query   = [];
        } else {
            $apiPath = 'Parcel/GET_MyParcels';
            $query   = ['user_id' => $user->id];
        }

        try {
            $res = $this->apiGet($apiPath, $query)->json();
            return response()->json(['data' => ($res['Success'] ?? false) ? ($res['Data'] ?? []) : []]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }
}
