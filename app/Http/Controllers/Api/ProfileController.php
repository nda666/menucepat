<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\PasswordRequest;
use App\Http\Resources\Api\UserResource;
use App\Repositories\UserRepository;

class ProfileController extends Controller
{

    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Update the user profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $updateProfileRequest)
    {
        $result = $this->userRepo->updateProfile($updateProfileRequest);
        return $result ? new UserResource($result) : response()->json(['message' => 'Data tidak dapat disimpan'], 500);
    }

    public function password(PasswordRequest $passwordRequest)
    {
        $result = $this->userRepo->changePassword($passwordRequest, auth()->user());
        return $result ? new UserResource($result) : response()->json(['message' => 'Password tidak dapat diubah'], 500);
    }
}
