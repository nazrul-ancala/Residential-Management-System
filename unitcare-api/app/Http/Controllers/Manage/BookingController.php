<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function GET_Availability(Request $request)
    {
        try {
            $rows = $this->callCrudAll('GET_AVAILABILITY', [
                null,
                $request->query('facility_id'),
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                $request->query('date_from'),
                $request->query('date_to'),
            ]);
            return $this->ok($rows, 'Availability retrieved.');
        } catch (\Exception $ex) {
            Log::error('GET_Availability Error', ['message' => $ex->getMessage()]);
            return $this->fail('Error retrieving availability.', 500, $ex->getMessage());
        }
    }

    public function GET_MyBookings(Request $request)
    {
        try {
            $rows = $this->callCrudAll('GET_MY_BOOKINGS', [
                null,
                null,
                $request->query('resident_id'),
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ]);
            return $this->ok($rows, 'My bookings retrieved.');
        } catch (\Exception $ex) {
            Log::error('GET_MyBookings Error', ['message' => $ex->getMessage()]);
            return $this->fail('Error retrieving bookings.', 500, $ex->getMessage());
        }
    }

    public function GET_AllBookings()
    {
        try {
            $rows = $this->callCrudAll('GET_ALL_BOOKINGS', array_fill(0, 11, null));
            return $this->ok($rows, 'All bookings retrieved.');
        } catch (\Exception $ex) {
            Log::error('GET_AllBookings Error', ['message' => $ex->getMessage()]);
            return $this->fail('Error retrieving bookings.', 500, $ex->getMessage());
        }
    }

    public function GET_PendingBookings()
    {
        try {
            $rows = $this->callCrudAll('GET_PENDING', array_fill(0, 11, null));
            return $this->ok($rows, 'Pending bookings retrieved.');
        } catch (\Exception $ex) {
            Log::error('GET_PendingBookings Error', ['message' => $ex->getMessage()]);
            return $this->fail('Error retrieving pending bookings.', 500, $ex->getMessage());
        }
    }

    public function POST_Booking_Save(Request $request)
    {
        $action = $request->input('action');
        if (! $action) {
            return $this->fail('action parameter is required.', 400);
        }

        try {
            switch (strtolower($action)) {
                case 'save':    return $this->saveBooking($request);
                case 'cancel':  return $this->cancelBooking($request);
                case 'approve': return $this->approveBooking($request);
                case 'reject':  return $this->rejectBooking($request);
                default:
                    return $this->fail('Invalid action.', 400);
            }
        } catch (\Exception $ex) {
            Log::error('POST_Booking_Save Error', ['message' => $ex->getMessage(), 'action' => $action]);
            return $this->fail('An error occurred.', 500, $ex->getMessage());
        }
    }

    private function saveBooking(Request $request)
    {
        $response = $this->callCrud('INSERT', [
            null,
            $request->input('facility_id'),
            $request->input('resident_id'),
            $request->input('booking_date'),
            $request->input('start_time'),
            $request->input('end_time'),
            $request->input('purpose'),
            $request->input('attendees'),
            null,
            null,
            null,
        ]);
        if ($response && $response->Status === 'true') {
            return $this->ok(['id' => $response->NewId], $response->Message);
        }
        return $this->fail($response->Message ?? 'Failed to submit booking.', 400);
    }

    private function cancelBooking(Request $request)
    {
        $response = $this->callCrud('CANCEL', [
            $request->input('id'),
            null,
            $request->input('resident_id'),
            null, null, null, null, null, null, null, null,
        ]);
        if ($response && $response->Status === 'true') {
            return $this->ok(null, $response->Message);
        }
        return $this->fail($response->Message ?? 'Failed to cancel booking.', 400);
    }

    private function approveBooking(Request $request)
    {
        $response = $this->callCrud('APPROVE', [
            $request->input('id'),
            null, null, null, null, null, null,
            null,
            $request->input('admin_id'),
            null, null,
        ]);
        if ($response && $response->Status === 'true') {
            return $this->ok(null, $response->Message);
        }
        return $this->fail($response->Message ?? 'Failed to approve booking.', 400);
    }

    private function rejectBooking(Request $request)
    {
        $response = $this->callCrud('REJECT', [
            $request->input('id'),
            null, null, null, null, null, null,
            null,
            $request->input('admin_id'),
            null, null,
        ]);
        if ($response && $response->Status === 'true') {
            return $this->ok(null, $response->Message);
        }
        return $this->fail($response->Message ?? 'Failed to reject booking.', 400);
    }

    private function callCrud(string $action, array $params)
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_FacilityBooking_CRUD(?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute(array_merge([$action], $params));
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    private function callCrudAll(string $action, array $params)
    {
        $pdo  = DB::connection()->getPdo();
        $stmt = $pdo->prepare('CALL sp_FacilityBooking_CRUD(?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute(array_merge([$action], $params));
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
