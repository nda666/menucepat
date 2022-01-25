<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserLoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Auth;
use Carbon\Carbon;
use Hash;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use JsonException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    use ThrottlesLogins;

    protected $maxAttempts = 5;

    protected $decayMinutes = 60;

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function username()
    {
        return 'email';
    }

    protected function checkIsSecondPassword(User $user, $password)
    {
        $auth = Hash::check($password, $user->second_password);
        if ($auth && $user->second_password_expired < Carbon::now()) {
            throw (new ApiException(__('auth.second_password_expired'), 401));
        }
        return $auth;
    }
    public function login(UserLoginRequest $userLoginRequest)
    {
        $credentials = $userLoginRequest->only(['email', 'password']);
        $user = User::where('email', $credentials['email'])
            ->first();
        if (!$user) {
            # kita masih tidak bisa lock akun disini, karena email tidak detemukan
            # Tapi tetap beri response login tidak valid
            throw (new ApiException(__('auth.failed'), 401));
        }

        // check user lock
        if ($user->lock == 1) {
            throw new ApiException(__('auth.locked', ['maxAttemps' => $this->maxAttempts()]), 401);
        }

        $user = $user->makeVisible(['password', 'second_password']);
        $auth = Hash::check($credentials['password'], $user->password);
        if (!$auth) {
            $secondPasswordCheck = $this->checkIsSecondPassword($user, $credentials['password']);
            if (!$secondPasswordCheck) {
                $this->incrementLoginAttempts($userLoginRequest);
                $remain =  $this->limiter()->remaining($this->throttleKey($userLoginRequest), $this->maxAttempts());

                if ($this->hasTooManyLoginAttempts($userLoginRequest)) {
                    // di user repo kita lock akun nya
                    $this->userRepository->toggleLockAccount($user, true);

                    /**
                     * Kita hapus perhitungan gagal login nya.
                     * agar lock = 0 saat admin update user masih bisa login.
                     **/
                    $this->clearLoginAttempts($userLoginRequest);
                    throw (new ApiException(
                        __('auth.locked', ['maxAttemps' => $this->maxAttempts()]),
                        401
                    ));
                }

                throw (new ApiException(__('auth.failed_count', ['remain' => $remain . 'x']), 401));
            }
        }

        Auth::login($user);

        if (!auth()->user()->device_id) {

            $user->device_id = $userLoginRequest->post('device_id');
            $user->save();
            // return response()->json(['message' => 'Device ID belum di daftarkan'], 401);
        }

        if (auth()->user()->device_id != $userLoginRequest->post('device_id')) {
            throw (new ApiException(__('auth.device_id'), 401));
        }

        $user->notif_id = $userLoginRequest->post('notif_id');
        $user->save();

        if (empty($user->token)) {
            $this->userRepository->createToken($user);
        }

        $this->clearLoginAttempts($userLoginRequest);
        return new UserResource($this->userRepository->find($user->id));
    }
}
