<?php

namespace App\Http\Controllers\utilities;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

class NavbarController extends Controller
{
    public function dashboardIsActive()
    {
        return Route::currentRouteName() === 'dashboard_utama' ? 'active' : '';
    }

    public function isActive(string $routeName): string
    {
        return Route::currentRouteName() === $routeName ? 'active' : '';
    }

    private function expanded(array $routes): string
    {
        return in_array(Route::currentRouteName(), $routes) ? 'active' : '';
    }

    // ── Section: Manage (admin) ─────────────────────────────────────────────
    public function manageIsExpanded(): string
    {
        return $this->expanded(['manageResidents', 'manageFacilities', 'manageUsers', 'manageStaff']);
    }

    // ── Section: Maintenance ───────────────────────────────────────────────
    public function maintenanceIsExpanded(): string
    {
        return $this->expanded(['manageAllRequests', 'manageMyRequests', 'myTasks']);
    }

    // ── Section: Visitors ─────────────────────────────────────────────────
    public function visitorsIsExpanded(): string
    {
        return $this->expanded(['registerVisitor', 'myVisitors', 'checkIn', 'todayVisitors']);
    }

    // ── Section: Bookings ─────────────────────────────────────────────────
    public function bookingsIsExpanded(): string
    {
        return $this->expanded(['bookFacility', 'myBookings', 'manageAllBookings']);
    }

    // ── Section: Announcements ────────────────────────────────────────────
    public function announcementsIsExpanded(): string
    {
        return $this->expanded(['manageAnnouncements', 'viewAnnouncements']);
    }

    // ── Section: Parcel Pickups ───────────────────────────────────────────
    public function parcelsIsExpanded(): string
    {
        return $this->expanded(['parcels.log', 'parcels.pending', 'manageParcels', 'myParcels']);
    }

}
