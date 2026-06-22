<?php

namespace App\Http\Controllers\requests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class manageMyRequestsController extends Controller
{
    public function index()
    {
        return view('requests.myRequests');
    }

    public function getMyRequestList()
    {
        $url = config('api.url') . '/api/Request/GET_MyRequestList?user_id=' . Auth::id();

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

    public function getComments(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $url = config('api.url') . '/api/Comment/GET_CommentList?ticket_id=' . $ticketId;

        try {
            /** @var Response $response */
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

    public function postComment(Request $request)
    {
        $url = config('api.url') . '/api/Comment/POST_Comment_Save';

        try {
            /** @var Response $response */
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->post($url, [
                    'action'    => 'save',
                    'ticket_id' => $request->input('ticket_id'),
                    'user_id'   => Auth::id(),
                    'comment'   => $request->input('comment'),
                ]);

            $apiData = $response->json();

            return response()->json([
                'message' => $apiData['Message'] ?? 'Operation completed',
                'status'  => $apiData['Success'] ?? false,
                'data'    => $apiData['Data'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function POST_Request_SaveUpdateDelete(Request $request)
    {
        $request->validate([
            'category'      => 'nullable|string|max:50',
            'priority'      => 'nullable|string|in:low,medium,high,urgent',
            'description'   => 'nullable|string',
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachments')) {
            $paths = [];
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('attachments/requests', 'public');
            }
            $attachmentPath = json_encode($paths);
        }

        $url = config('api.url') . '/api/Request/POST_Request_SaveUpdateDelete';

        try {
            /** @var Response $response */
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->post($url, [
                    'action'          => $request->input('action', 'save'),
                    'id'              => $request->id,
                    'user_id'         => Auth::id(),
                    'category'        => $request->category,
                    'priority'        => $request->priority,
                    'description'     => $request->description,
                    'attachment_path' => $attachmentPath,
                    'status'          => $request->status,
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
