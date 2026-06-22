<?php

namespace App\Http\Controllers\announcements;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class viewAnnouncementsController extends Controller
{
    public function index()
    {
        return view('announcements.viewAnnouncements');
    }

    public function getList()
    {
        $apiUrl = config('api.url') . '/api/Announcement/GET_PublishedAnnouncements';

        try {
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withoutVerifying()
                ->get($apiUrl);

            $data = $response->json();

            return response()->json([
                'data' => ($data['Success'] ?? false) ? ($data['Data'] ?? []) : [],
            ]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }
}
