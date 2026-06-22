<?php

namespace App\Http\Controllers\parcels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class manageLogParcelController extends Controller
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
        return view('parcels.logParcel');
    }

    public function getResidentList()
    {
        try {
            $res = $this->apiGet('Resident/GET_ResidentList')->json();
            return response()->json(['data' => ($res['Success'] ?? false) ? ($res['Data'] ?? []) : []]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'resident_id' => 'required|integer',
            'courier'     => 'required|string|max:100',
            'parcel_type' => 'nullable|in:letter,small_box,large_box,fragile',
            'tracking_no' => 'nullable|string|max:100',
            'notes'       => 'nullable|string|max:1000',
            'photo'       => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $path      = $request->file('photo')->store('parcels', 'public');
            $photoPath = $path;
        }

        try {
            $res = $this->apiPost('Parcel/POST_Parcel_Save', [
                'action'      => 'log',
                'resident_id' => $request->input('resident_id'),
                'logged_by'   => Auth::id(),
                'courier'     => $request->input('courier'),
                'parcel_type' => $request->input('parcel_type', 'small_box'),
                'tracking_no' => $request->input('tracking_no'),
                'notes'       => $request->input('notes'),
                'photo_path'  => $photoPath,
            ])->json();

            if ($res['Success'] ?? false) {
                $pin = $res['Data']['collection_pin'] ?? null;

                $this->notifyResident(
                    $request->input('resident_id'),
                    $request->input('courier'),
                    $request->input('parcel_type', 'small_box'),
                    $pin
                );

                return response()->json(['status' => true, 'message' => $res['Message'] ?? 'Parcel logged successfully.']);
            }

            return response()->json(['status' => false, 'message' => $res['Message'] ?? 'Failed to log parcel.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function notifyResident(int $residentId, string $courier, string $parcelType, ?string $pin = null)
    {
        try {
            $typeLabel = str_replace('_', ' ', $parcelType);
            $message   = "You have a {$typeLabel} from {$courier} waiting at the lobby.";
            if ($pin) {
                $message .= " Collection PIN: {$pin}";
            }

            \DB::table('notifications')->insert([
                'id'              => \Str::uuid(),
                'type'            => 'App\Notifications\ParcelArrived',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $residentId,
                'data'            => json_encode([
                    'title'          => 'Parcel Arrived',
                    'message'        => $message,
                    'collection_pin' => $pin,
                ]),
                'read_at'    => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Non-critical — notification failure should not block the parcel log
        }
    }
}
