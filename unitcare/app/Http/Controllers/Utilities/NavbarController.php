<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

class NavbarController extends Controller
{
    public function pagesIsExpanded($class)
    {
        $routes = ['dashboard_utama', 'manageMaintenance'];

        return in_array(Route::currentRouteName(), $routes) ? $class : '';
    }

    public function pagesIsActive($class)
    {
        $routes = ['dashboard_utama', 'manageMaintenance'];

        return in_array(Route::currentRouteName(), $routes) ? $class : '';
    }

    public function dashboardIsActive($routeName)
    {
        return Route::currentRouteName() === $routeName ? 'active' : '';
    }

    public function maintenanceIsActive($routeName)
    {
        return Route::currentRouteName() === $routeName ? 'active' : '';
    }
}
