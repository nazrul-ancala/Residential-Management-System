<?php

namespace App\Http\Controllers\maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class manageMaintenanceController extends Controller
{
    public function index()
    {
        return view('maintenance.manageMaintenance');
    }
}
