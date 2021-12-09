<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Password;

class PasswordController extends Controller
{
    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );
        return response()->json(__($status), $status === Password::RESET_LINK_SENT ? 200 : 501);
    }
}
