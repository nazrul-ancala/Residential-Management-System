<?php

use App\Http\Controllers\Manage\FacilityController;
use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\Manage\ResidentController;
use App\Http\Controllers\Manage\RequestController;
use App\Http\Controllers\Manage\CommentController;
use App\Http\Controllers\Manage\VisitorController;
use App\Http\Controllers\Manage\AnnouncementController;
use App\Http\Controllers\Manage\BookingController;
use App\Http\Controllers\Manage\ReportController;
use App\Http\Controllers\Manage\ParcelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Data endpoints are consumed server-side by the UnitCare frontend, which
| sends HTTP Basic credentials (TOKEN_PASS1 / TOKEN_PASS2). The CRUD work
| itself is delegated to MySQL stored procedures (see database/).
|
*/

// Health check (no auth required)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'UnitCare API is running',
        'timestamp' => now()->toDateTimeString(),
    ]);
});

// Authenticated data endpoints (HTTP Basic token)
Route::middleware('basic.token')->group(function () {

    // Facilities
    Route::get('/Facility/GET_FacilityList', [FacilityController::class, 'GET_FacilityList']);
    Route::get('/Facility/GET_SpecificFacility', [FacilityController::class, 'GET_SpecificFacility']);
    Route::post('/Facility/POST_Facility_SaveUpdateDelete', [FacilityController::class, 'POST_Facility_SaveUpdateDelete']);

    // Users
    Route::get('/User/GET_UserList', [UserController::class, 'GET_UserList']);
    Route::get('/User/GET_StaffList', [UserController::class, 'GET_StaffList']);
    Route::get('/User/GET_SpecificUser', [UserController::class, 'GET_SpecificUser']);
    Route::post('/User/POST_User_SaveUpdateDelete', [UserController::class, 'POST_User_SaveUpdateDelete']);

    // Residents
    Route::get('/Resident/GET_ResidentList', [ResidentController::class, 'GET_ResidentList']);
    Route::post('/Resident/POST_Resident_SaveUpdateDelete', [ResidentController::class, 'POST_Resident_SaveUpdateDelete']);

    // Maintenance Requests
    Route::get('/Request/GET_RequestList', [RequestController::class, 'GET_RequestList']);
    Route::get('/Request/GET_MyRequestList', [RequestController::class, 'GET_MyRequestList']);
    Route::get('/Request/GET_SpecificRequest', [RequestController::class, 'GET_SpecificRequest']);
    Route::get('/Request/GET_AssignedRequestList', [RequestController::class, 'GET_AssignedRequestList']);
    Route::post('/Request/POST_Request_SaveUpdateDelete', [RequestController::class, 'POST_Request_SaveUpdateDelete']);

    // Maintenance Comments
    Route::get('/Comment/GET_CommentList', [CommentController::class, 'GET_CommentList']);
    Route::post('/Comment/POST_Comment_Save', [CommentController::class, 'POST_Comment_Save']);

    // Visitors
    Route::get('/Visitor/GET_VisitorList',    [VisitorController::class, 'GET_VisitorList']);
    Route::get('/Visitor/GET_MyVisitorList',  [VisitorController::class, 'GET_MyVisitorList']);
    Route::get('/Visitor/GET_TodayVisitors',  [VisitorController::class, 'GET_TodayVisitors']);
    Route::post('/Visitor/POST_Visitor_Save', [VisitorController::class, 'POST_Visitor_Save']);
    Route::post('/Visitor/AUTO_Checkout',     [VisitorController::class, 'AUTO_Checkout']);

    // Announcements
    Route::get('/Announcement/GET_AllAnnouncements',       [AnnouncementController::class, 'GET_AllAnnouncements']);
    Route::get('/Announcement/GET_PublishedAnnouncements', [AnnouncementController::class, 'GET_PublishedAnnouncements']);
    Route::post('/Announcement/POST_Announcement_Save',    [AnnouncementController::class, 'POST_Announcement_Save']);

    // Bookings
    Route::get('/Booking/GET_Availability',    [BookingController::class, 'GET_Availability']);
    Route::get('/Booking/GET_MyBookings',      [BookingController::class, 'GET_MyBookings']);
    Route::get('/Booking/GET_AllBookings',     [BookingController::class, 'GET_AllBookings']);
    Route::get('/Booking/GET_PendingBookings', [BookingController::class, 'GET_PendingBookings']);
    Route::post('/Booking/POST_Booking_Save',  [BookingController::class, 'POST_Booking_Save']);

    // Parcels
    Route::get('/Parcel/GET_AllParcels',     [ParcelController::class, 'GET_AllParcels']);
    Route::get('/Parcel/GET_PendingParcels', [ParcelController::class, 'GET_PendingParcels']);
    Route::get('/Parcel/GET_MyParcels',      [ParcelController::class, 'GET_MyParcels']);
    Route::post('/Parcel/POST_Parcel_Save',  [ParcelController::class, 'POST_Parcel_Save']);

    // Reports
    Route::get('/Report/GET_ReportSummary', [ReportController::class, 'GET_ReportSummary']);
    Route::get('/Report/GET_ReportData',    [ReportController::class, 'GET_ReportData']);

});
