<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class registerController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([...$data, 'role' => 'tenant']);

        Auth::login($user);

        return $this->redirectByRole($user->role);
    }

    private function redirectByRole(string $role): \Illuminate\Http\RedirectResponse
    {
        return match($role) {
            'tenant'     => redirect()->route('manageMyRequests'),
            'technician' => redirect()->route('manageMyJobs'),
            default      => redirect()->route('dashboard_utama'),
        };
    }
}
