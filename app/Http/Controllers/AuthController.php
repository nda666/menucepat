<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate(['username' => 'required']);
        $credentials = $request->only(['username', 'password']);

        $user = Auth::guard('admin')->attempt($credentials, $request->post('remember'));
        if (!$user) {
            return redirect()->route('login')->withErrors(['message' => 'Login tidak valid']);
        }
        return redirect()->route('dashboard');
    }
}
