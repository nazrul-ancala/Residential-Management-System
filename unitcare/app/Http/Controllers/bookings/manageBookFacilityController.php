<?php

namespace App\Http\Controllers\bookings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class manageBookFacilityController extends Controller
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
        $facilities = [];
        try {
            $res = $this->apiGet('Facility/GET_FacilityList')->json();
            if ($res['Success'] ?? false) {
                $facilities = array_filter($res['Data'] ?? [], fn($f) => ($f['status'] ?? '') === 'available');
                $facilities = array_values($facilities);
            }
        } catch (\Exception $e) {}

        return view('bookings.bookFacility', compact('facilities'));
    }

    public function getFacilityAvailability(Request $request)
    {
        try {
            $res = $this->apiGet('Booking/GET_Availability', [
                'facility_id' => $request->query('facility_id'),
                'date_from'   => $request->query('date_from'),
                'date_to'     => $request->query('date_to'),
            ])->json();
            return response()->json(['data' => ($res['Success'] ?? false) ? ($res['Data'] ?? []) : []]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $purpose = trim($request->input('purpose', ''));
        if (!$purpose) {
            return response()->json(['status' => false, 'message' => 'Purpose is required.']);
        }
        if (mb_strlen($purpose) > 500) {
            return response()->json(['status' => false, 'message' => 'Purpose must not exceed 500 characters.']);
        }

        try {
            $res = $this->apiPost('Booking/POST_Booking_Save', [
                'action'       => 'save',
                'facility_id'  => $request->input('facility_id'),
                'resident_id'  => Auth::id(),
                'booking_date' => $request->input('booking_date'),
                'start_time'   => $request->input('start_time'),
                'end_time'     => $request->input('end_time'),
                'purpose'      => $purpose,
                'attendees'    => $request->input('attendees'),
            ])->json();
            return response()->json([
                'status'  => $res['Success']  ?? false,
                'message' => $res['Message']  ?? 'Operation completed.',
                'data'    => $res['Data']      ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
