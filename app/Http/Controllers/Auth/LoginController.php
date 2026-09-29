<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'employee_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt([
            'employee_number' => $credentials['employee_number'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $remember)) {

            $request->session()->regenerate();

            $user = $request->user();

            $redirectPath = match (true) {
                $user->hasRole('employee') => '/dashboard',

                $user->hasRole('it_staff') => '/it/dashboard',

                $user->hasRole('mis_supervisor') => '/tickets',

                $user->hasRole('admin') => '/it/dashboard',

                default => '/dashboard',
            };

            return redirect()->intended($redirectPath);
        }

        return back()
            ->withErrors([
                'employee_number' => 'NIP atau password salah.',
            ])
            ->onlyInput('employee_number');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
