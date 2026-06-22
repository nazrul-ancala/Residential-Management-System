<?php

namespace App\Http\Controllers\announcements;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class manageAnnouncementsController extends Controller
{
    public function index()
    {
        return view('announcements.manageAnnouncements');
    }

    public function getList()
    {
        $apiUrl = config('api.url') . '/api/Announcement/GET_AllAnnouncements';

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

    public function uploadImage(Request $request)
    {
        $request->validate(['image' => 'required|image|max:2048']);
        $path = $request->file('image')->store('announcements', 'public');
        return response()->json(['status' => true, 'path' => Storage::url($path)]);
    }

    public function store(Request $request)
    {
        $apiUrl = config('api.url') . '/api/Announcement/POST_Announcement_Save';

        try {
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->post($apiUrl, [
                    'action'       => $request->input('action', 'save'),
                    'id'           => $request->input('id'),
                    'user_id'      => Auth::id(),
                    'title'        => $request->input('title'),
                    'content'      => $request->input('content'),
                    'type'         => $request->input('type'),
                    'published_at' => $request->input('published_at'),
                    'image_path'   => $request->input('image_path'),
                ]);

            $data = $response->json();

            return response()->json([
                'status'  => $data['Success']  ?? false,
                'message' => $data['Message']  ?? 'Operation completed.',
                'data'    => $data['Data']      ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
