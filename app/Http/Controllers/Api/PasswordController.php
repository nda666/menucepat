<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Password;

class PasswordController extends Controller
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $this->userRepo->resetPassword($request);

        return response()->json(['success' => true, 'message' => 'Kami sudah mengirim surel yang berisi password baru untuk Anda']);
    }
}
