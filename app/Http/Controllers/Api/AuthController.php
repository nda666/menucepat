<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserLoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Auth;
use Hash;
use Illuminate\Foundation\Auth\ThrottlesLogins;

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

    public function login(UserLoginRequest $userLoginRequest)
    {
        $credentials = $userLoginRequest->only(['email', 'password']);
        $user = User::where('email', $credentials['email'])
            ->first();

        if (!$user) {
            /**
             * kita masih tidak bisa lock akun disini, karena email tidak detemukan
             * Tapi tetap beri response login tidak valid
             * 
             **/
            return response()->json(['message' => 'Data login tidak valid.'], 401);
        }


        // check user lock
        if ($user->lock == 1) {
            return response()->json(['message' => "Akun anda sedang di kunci karena gagal login {$this->maxAttempts}x, silahkan hubungi Admin."], 401);
        }
        $user = $user->makeVisible(['password']);
        $auth = Hash::check($credentials['password'], $user->password);
        if (!$auth) {
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
                return response()->json(['message' => "Akun anda terkunci karena gagal login {$this->maxAttempts}x. Silahkan hubungi Admin"], 401);
            }

            return response()->json(['message' => "Data login tidak valid. Tersisa {$remain}x kesempatan."], 401);
        }

        Auth::login($user);

        if (!auth()->user()->device_id) {

            $user->device_id = $userLoginRequest->post('device_id');
            $user->save();
            // return response()->json(['message' => 'Device ID belum di daftarkan'], 401);
        }

        if (auth()->user()->device_id != $userLoginRequest->post('device_id')) {
            return response()->json(['message' => 'Device ID tidak sesuai dengan server'], 401);
        }

        $user->notif_id = $userLoginRequest->post('notif_id');
        $user->save();

        $this->clearLoginAttempts($userLoginRequest);
        return new UserResource($this->userRepository->find($user->id));
    }
}
