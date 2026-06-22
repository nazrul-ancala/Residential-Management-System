<?php

use App\Http\Controllers\auth\loginController;
use App\Http\Controllers\dashboard\manageDashboardController;
use App\Http\Controllers\manage\manageUsersController;
use App\Http\Controllers\manage\manageResidentsController;
use App\Http\Controllers\manage\manageFacilitiesController;
use App\Http\Controllers\manage\manageStaffController;
// Visitors
use App\Http\Controllers\visitors\manageRegisterVisitorController;
use App\Http\Controllers\visitors\manageMyVisitorsController;
use App\Http\Controllers\visitors\manageCheckInController;
use App\Http\Controllers\visitors\manageTodayVisitorsController;
// Maintenance
use App\Http\Controllers\requests\manageAllRequestsController;
use App\Http\Controllers\requests\manageMyRequestsController;
use App\Http\Controllers\requests\manageMyTasksController;
// Bookings
use App\Http\Controllers\bookings\manageBookFacilityController;
use App\Http\Controllers\bookings\manageMyBookingsController;
use App\Http\Controllers\bookings\manageAllBookingsController;
// Announcements
use App\Http\Controllers\announcements\manageAnnouncementsController;
use App\Http\Controllers\announcements\viewAnnouncementsController;
use App\Http\Controllers\reports\manageReportsController;
use App\Http\Controllers\auth\profileController;
use App\Http\Controllers\Utilities\NotificationController;
// Parcels
use App\Http\Controllers\parcels\manageLogParcelController;
use App\Http\Controllers\parcels\managePendingParcelsController;
use App\Http\Controllers\parcels\manageAllParcelsController;
use App\Http\Controllers\parcels\manageMyParcelsController;
use Illuminate\Support\Facades\Route;

// Landing (public)
Route::view('/', 'landing.homepage')->name('homepage');

// Auth (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login',    [loginController::class, 'index'])->name('login');
    Route::post('/login',   [loginController::class, 'store'])->name('login.post');
});

Route::post('/logout', [loginController::class, 'destroy'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard — all roles
    Route::get('/dashboard', [manageDashboardController::class, 'index'])->name('dashboard_utama');

    // Notifications — all roles
    Route::get('/notifications/count',    [NotificationController::class, 'getUnreadCount'])->name('notifications.count');
    Route::get('/notifications',          [NotificationController::class, 'getList'])->name('notifications.list');
    Route::post('/notifications/read-all',[NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // Profile — all roles
    Route::get('/profile',           [profileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit',      [profileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update',   [profileController::class, 'update'])->name('profile.update');
    Route::get('/profile/settings',  [profileController::class, 'settings'])->name('profile.settings');
    Route::post('/profile/password', [profileController::class, 'updatePassword'])->name('profile.password');

    // ── Admin only ──────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        // Parcel Pickups — admin management
        Route::get('/parcels/manage',        [manageAllParcelsController::class, 'index'])->name('manageParcels');
        Route::get('/parcels/manage/list',   [manageAllParcelsController::class, 'getAllParcelList'])->name('allParcels.list');
        Route::get('/parcels/manage/pending',[manageAllParcelsController::class, 'getPendingList'])->name('allParcels.pending');
        Route::post('/parcels/manage/update',[manageAllParcelsController::class, 'updateParcel'])->name('allParcels.update');
        Route::get('/users',              [manageUsersController::class, 'index'])->name('manageUsers');
        Route::get('/users/list',         [manageUsersController::class, 'getUserList'])->name('users.list');
        Route::post('/users/save',        [manageUsersController::class, 'POST_User_SaveUpdateDelete'])->name('users.save');
        Route::get('/residents',          [manageResidentsController::class, 'index'])->name('manageResidents');
        Route::get('/residents/list',     [manageResidentsController::class, 'getResidentList'])->name('residents.list');
        Route::post('/residents/save',   [manageResidentsController::class, 'POST_Resident_SaveUpdateDelete'])->name('residents.save');
        Route::get('/facilities',         [manageFacilitiesController::class, 'index'])->name('manageFacilities');
        Route::get('/facilities/list',    [manageFacilitiesController::class, 'getFacilityList'])->name('facilities.list');
        Route::get('/facilities/info',    [manageFacilitiesController::class, 'getFacilityInformation'])->name('facilities.info');
        Route::post('/facilities/save',   [manageFacilitiesController::class, 'POST_Facility_SaveUpdateDelete'])->name('facilities.save');
        // Staff management
        Route::get('/staff',              [manageStaffController::class, 'index'])->name('manageStaff');
        Route::get('/staff/list',         [manageStaffController::class, 'getStaffList'])->name('staff.list');
        Route::post('/staff/save',        [manageStaffController::class, 'POST_Staff_SaveUpdateDelete'])->name('staff.save');
        // Maintenance (admin)
        Route::get('/maintenance/all',    [manageAllRequestsController::class, 'index'])->name('manageAllRequests');
        Route::get('/maintenance/all/list',     [manageAllRequestsController::class, 'getAllRequestList'])->name('allRequests.list');
        Route::get('/maintenance/all/staff',    [manageAllRequestsController::class, 'getStaffList'])->name('allRequests.staff');
        Route::post('/maintenance/all/save',    [manageAllRequestsController::class, 'POST_Request_SaveUpdateDelete'])->name('allRequests.save');
        Route::get('/maintenance/all/comments', [manageAllRequestsController::class, 'getComments'])->name('allRequests.comments');
        Route::post('/maintenance/all/comments',[manageAllRequestsController::class, 'postComment'])->name('allRequests.comment.save');
        // Bookings / Announcements / Reports (admin)
        Route::get('/bookings/manage',         [manageAllBookingsController::class, 'index'])->name('manageAllBookings');
        Route::get('/bookings/manage/list',    [manageAllBookingsController::class, 'getAllBookingList'])->name('allBookings.list');
        Route::get('/bookings/manage/pending', [manageAllBookingsController::class, 'getPendingList'])->name('allBookings.pending');
        Route::post('/bookings/manage/update', [manageAllBookingsController::class, 'updateBooking'])->name('allBookings.update');
        Route::get('/announcements/manage',      [manageAnnouncementsController::class, 'index'])->name('manageAnnouncements');
        Route::get('/announcements/manage/list', [manageAnnouncementsController::class, 'getList'])->name('manageAnnouncements.list');
        Route::post('/announcements/manage/store',[manageAnnouncementsController::class, 'store'])->name('announcement.store');
        Route::post('/announcements/manage/upload-image', [manageAnnouncementsController::class, 'uploadImage'])->name('announcement.uploadImage');
        Route::get('/reports',            [manageReportsController::class, 'index'])->name('manageReports');
        Route::get('/reports/summary',    [manageReportsController::class, 'getReportSummary'])->name('reports.summary');
        Route::get('/reports/data',       [manageReportsController::class, 'getReportData'])->name('reports.data');
    });

    // ── Resident + Admin ────────────────────────────────────────────────────
    Route::middleware('role:resident,admin')->group(function () {
        Route::get('/visitors/register',       [manageRegisterVisitorController::class, 'index'])->name('registerVisitor');
        Route::post('/visitors/register/save', [manageRegisterVisitorController::class, 'store'])->name('visitor.store');
        Route::get('/visitors/residents',      [manageRegisterVisitorController::class, 'getResidentList'])->name('visitor.residents');
        Route::get('/visitors/my',             [manageMyVisitorsController::class, 'index'])->name('myVisitors');
        Route::get('/visitors/my/list',        [manageMyVisitorsController::class, 'getVisitorList'])->name('myVisitors.list');
        Route::get('/maintenance/my',     [manageMyRequestsController::class, 'index'])->name('manageMyRequests');
        Route::get('/maintenance/my/list',      [manageMyRequestsController::class, 'getMyRequestList'])->name('myRequests.list');
        Route::post('/maintenance/my/save',     [manageMyRequestsController::class, 'POST_Request_SaveUpdateDelete'])->name('myRequests.save');
        Route::get('/maintenance/my/comments',  [manageMyRequestsController::class, 'getComments'])->name('myRequests.comments');
        Route::post('/maintenance/my/comments', [manageMyRequestsController::class, 'postComment'])->name('myRequests.comment.save');
        Route::get('/bookings/book',             [manageBookFacilityController::class, 'index'])->name('bookFacility');
        Route::get('/bookings/book/availability',[manageBookFacilityController::class, 'getFacilityAvailability'])->name('bookings.availability');
        Route::post('/bookings/book/save',       [manageBookFacilityController::class, 'store'])->name('booking.store');
        Route::get('/bookings/my',               [manageMyBookingsController::class, 'index'])->name('myBookings');
        Route::get('/bookings/my/list',          [manageMyBookingsController::class, 'getMyBookingList'])->name('myBookings.list');
        Route::post('/bookings/my/cancel',       [manageMyBookingsController::class, 'cancelBooking'])->name('myBookings.cancel');
    });

    // ── Technician only ─────────────────────────────────────────────────────
    Route::middleware('role:technician')->group(function () {
        Route::get('/tasks',             [manageMyTasksController::class, 'index'])->name('myTasks');
        Route::get('/tasks/list',        [manageMyTasksController::class, 'getMyTaskList'])->name('myTasks.list');
        Route::post('/tasks/save',       [manageMyTasksController::class, 'updateTask'])->name('myTasks.save');
        Route::get('/tasks/comments',    [manageMyTasksController::class, 'getComments'])->name('myTasks.comments');
        Route::post('/tasks/comments',   [manageMyTasksController::class, 'postComment'])->name('myTasks.comment.save');
    });

    // ── Security + Admin ────────────────────────────────────────────────────
    Route::middleware('role:security,admin')->group(function () {
        Route::get('/visitors/check-in',   [manageCheckInController::class, 'index'])->name('checkIn');
        Route::get('/visitors/search',     [manageCheckInController::class, 'search'])->name('visitor.search');
        Route::post('/visitors/checkin',   [manageCheckInController::class, 'processCheckIn'])->name('visitor.checkin');
        Route::get('/visitors/today',      [manageTodayVisitorsController::class, 'index'])->name('todayVisitors');
        Route::get('/visitors/today/list', [manageTodayVisitorsController::class, 'getTodayList'])->name('todayVisitors.list');
        // Parcel Pickups — log and mark collected
        Route::get('/parcels/log',            [manageLogParcelController::class, 'index'])->name('parcels.log');
        Route::get('/parcels/log/residents',  [manageLogParcelController::class, 'getResidentList'])->name('parcels.log.residents');
        Route::post('/parcels/log/save',      [manageLogParcelController::class, 'store'])->name('parcels.log.save');
        Route::get('/parcels/pending',        [managePendingParcelsController::class, 'index'])->name('parcels.pending');
        Route::get('/parcels/pending/list',   [managePendingParcelsController::class, 'getPendingList'])->name('parcels.pending.list');
        Route::post('/parcels/collect',       [managePendingParcelsController::class, 'collectParcel'])->name('parcels.collect');
    });

    // ── Resident + Admin ────────────────────────────────────────────────────
    // (Parcel: resident views own parcels; admin already has manage route above)
    Route::middleware('role:resident,admin')->group(function () {
        Route::get('/parcels/my',      [manageMyParcelsController::class, 'index'])->name('myParcels');
        Route::get('/parcels/my/list', [manageMyParcelsController::class, 'getMyParcelList'])->name('myParcels.list');
    });

    // ── Resident + Security ─────────────────────────────────────────────────
    Route::middleware('role:resident,security')->group(function () {
        Route::get('/announcements',      [viewAnnouncementsController::class, 'index'])->name('viewAnnouncements');
        Route::get('/announcements/list', [viewAnnouncementsController::class, 'getList'])->name('viewAnnouncements.list');
    });

});

