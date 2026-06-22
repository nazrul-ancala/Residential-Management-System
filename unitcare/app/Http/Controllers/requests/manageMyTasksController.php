<?php

namespace App\Http\Controllers\requests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class manageMyTasksController extends Controller
{
    public function index()
    {
        return view('requests.myTasks');
    }

    public function getMyTaskList()
    {
        $url = config('api.url')
            . '/api/Request/GET_AssignedRequestList?user_id=' . Auth::id();

        try {
            /** @var Response $response */
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withoutVerifying()
                ->get($url);

            $apiData = $response->json();

            return response()->json([
                'data' => ($apiData['Success'] ?? false) ? ($apiData['Data'] ?? []) : [],
            ]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function updateTask(Request $request)
    {
        $url = config('api.url') . '/api/Request/POST_Request_SaveUpdateDelete';

        try {
            /** @var Response $response */
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->post($url, [
                    'action' => 'update',
                    'id'     => $request->input('id'),
                    'status' => $request->input('status'),
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

    public function getComments(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $url = config('api.url')
            . '/api/Comment/GET_CommentList?ticket_id=' . $ticketId;

        try {
            /** @var Response $response */
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withoutVerifying()
                ->get($url);

            $apiData = $response->json();

            return response()->json([
                'data' => ($apiData['Success'] ?? false) ? ($apiData['Data'] ?? []) : [],
            ]);
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
}
