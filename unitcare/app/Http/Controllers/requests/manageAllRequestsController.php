<?php

namespace App\Http\Controllers\requests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class manageAllRequestsController extends Controller
{
    public function index()
    {
        return view('requests.manageRequests');
    }

    public function getAllRequestList()
    {
        $url = config('api.url') . '/api/Request/GET_RequestList';

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

    public function getStaffList()
    {
        $url = config('api.url') . '/api/User/GET_StaffList';

        try {
            /** @var Response $response */
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withoutVerifying()
                ->get($url);

            $apiData = $response->json();

            // Only technicians can be assigned to maintenance tickets
            $data = array_values(array_filter(
                $apiData['Data'] ?? [],
                fn($s) => ($s['role'] ?? '') === 'technician'
            ));

            return response()->json(['data' => $data]);
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
        $url = config('api.url') . '/api/Request/POST_Request_SaveUpdateDelete';

        try {
            /** @var Response $response */
            $response = Http::timeout(60)
                ->withBasicAuth(config('api.pass1'), config('api.pass2'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->post($url, [
                    'action'      => $request->input('action', 'update'),
                    'id'          => $request->id,
                    'status'      => $request->status,
                    'assigned_to' => $request->assigned_to,
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
