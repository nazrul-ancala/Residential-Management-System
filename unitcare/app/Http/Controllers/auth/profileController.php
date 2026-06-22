<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class profileController extends Controller
{
    public function show()
    {
        return view('auth.profile', ['activeTab' => 'about']);
    }

    public function edit()
    {
        return view('auth.profile', ['activeTab' => 'edit']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|max:255',
            'phone' => 'nullable|max:20',
        ]);

        $request->user()->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function settings()
    {
        return view('auth.profile', ['activeTab' => 'settings']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Password changed. Please log in with your new password.');
    }
}
