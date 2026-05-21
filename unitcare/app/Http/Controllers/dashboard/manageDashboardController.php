<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;

class manageDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.dashboard_utama');
    }
}
