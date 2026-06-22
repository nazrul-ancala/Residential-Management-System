<?php

namespace App\Http\Controllers\manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class manageFacilitiesController extends Controller
{
    public function index()
    {
        return view('manage.manageFacilities');
    }

    /**
     * DataTable feed: GET facility list from the API.
     */
    public function getFacilityList()
    {
        $url = config('api.url') . '/api/Facility/GET_FacilityList';

        try {
            /** @var Response $response */
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withoutVerifying()
                ->get($url);

            $apiData = $response->json();

            if (isset($apiData['Success']) && $apiData['Success']) {
                return response()->json(['data' => $apiData['Data']]);
            }

            return response()->json(['data' => []]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * Single facility lookup (used by the edit modal if needed).
     */
    public function getFacilityInformation(Request $request)
    {
        $url = config('api.url') . '/api/Facility/GET_SpecificFacility?id=' . $request->input('id');

        try {
            /** @var Response $response */
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withoutVerifying()
                ->get($url);

            $apiData = $response->json();

            if (isset($apiData['Success']) && $apiData['Success']) {
                return response()->json(['data' => $apiData['Data']]);
            }

            return response()->json(['data' => []]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * Save / update / delete a facility via the API.
     */
    public function POST_Facility_SaveUpdateDelete(Request $request)
    {
        $url = config('api.url') . '/api/Facility/POST_Facility_SaveUpdateDelete';

        try {
            /** @var Response $response */
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->post($url, [
                    'action'            => $request->action,
                    'id'                => $request->id,
                    'name'              => $request->name,
                    'description'       => $request->description,
                    'max_booking_hours' => $request->max_booking_hours,
                    'capacity'          => $request->capacity,
                    'status'            => $request->status,
                ]);

            $apiData = $response->json();

            return response()->json([
                'message' => $apiData['Message'] ?? 'Operation completed',
                'status'  => $apiData['Success'] ?? false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
                'status'  => false,
            ], 500);
        }
    }
}
