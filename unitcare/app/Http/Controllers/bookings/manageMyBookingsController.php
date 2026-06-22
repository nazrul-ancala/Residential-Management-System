<?php

namespace App\Http\Controllers\bookings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class manageMyBookingsController extends Controller
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

    private function apiPost(string $path, array $data)
    {
        return Http::timeout(60)
            ->withBasicAuth(config('api.pass1'), config('api.pass2'))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->withoutVerifying()
            ->post($this->apiUrl($path), $data);
    }

    public function index()
    {
        return view('bookings.myBookings');
    }

    public function getMyBookingList()
    {
        try {
            $res = $this->apiGet('Booking/GET_MyBookings', ['resident_id' => Auth::id()])->json();
            return response()->json(['data' => ($res['Success'] ?? false) ? ($res['Data'] ?? []) : []]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function cancelBooking(Request $request)
    {
        try {
            $res = $this->apiPost('Booking/POST_Booking_Save', [
                'action'      => 'cancel',
                'id'          => $request->input('id'),
                'resident_id' => Auth::id(),
            ])->json();
            return response()->json([
                'status'  => $res['Success']  ?? false,
                'message' => $res['Message']  ?? 'Operation completed.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
