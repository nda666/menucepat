<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory(1)->create()->first();
        $this->user->email = 'adhabakhtiar@gmail.com';
        $this->user->save();
    }

    /** @test */
    function lockedUserShouldNotAbleToLogin()
    {
        $user = $this->user;
        $user->lock = 1;
        $user->save();

        $response = $this->post(route('api.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_id' => 'random_device_id'
        ]);

        $response->assertUnauthorized();
        $response->assertJson(['message' => __('auth.locked', ['maxAttemps' => 5])]);
    }

    /** @test */
    function new_user_login_should_get_a_token()
    {
        $user = $this->user;

        $response = $this->post(route('api.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_id' => 'random_device_id'
        ]);
        $response->assertJson([
            'data' => [
                'email' => $user->email,
                'device_id' => 'random_device_id',
            ],
            'success' => true,
        ]);
    }

    /** @test */
    function login_without_device_id_for_first_time_should_save_user_device_id()
    {
        $user = $this->user;
        $response = $this->post(route('api.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_id' => 'random_device_id'
        ]);
        $response->assertOk();
        // Log::error($response->json());
        $response->assertJson([
            'data' => [
                'email' => $user->email,
                'device_id' => 'random_device_id',
            ],
            'success' => true,
        ]);
    }

    /** @test */
    function login_with_unvalid_credential_should_response_401()
    {
        $user = $this->user;
        $user->device_id = 'random_device_id';
        $user->save();
        $response = $this->post(route('api.login'), [
            'email' => $user->email . 'wrong',
            'password' => 'password',
            'device_id' => 'random_device_id'
        ]);
        $response->assertUnauthorized();
        $response->assertJson(['message' => __('auth.failed')]);

        $response = $this->post(route('api.login'), [
            'email' => $user->email,
            'password' => 'password' . 'wrong',
            'device_id' => 'random_device_id'
        ]);
        $response->assertUnauthorized();
        $response->assertJson(['message' => __('auth.failed_count', ['remain' => '4x'])]);

        $response = $this->post(route('api.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_id' => 'random_device_id' . 'wrong',
        ]);
        $response->assertUnauthorized();
        $response->assertJson(['message' => __('auth.device_id')]);
    }

    /** @test */
    function login_with_unvalid_credential_5_times_should_lock_user()
    {
        $user = $this->user;
        $response = null;
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('api.login'), [
                'email' => $user->email,
                'password' => 'password' . 'wrong',
                'device_id' => 'random_device_id'
            ]);
        }
        $response->assertUnauthorized();
        $response->assertJson(['message' => __('auth.locked', ['maxAttemps' => 5])]);
        $user->refresh();
        $this->assertEquals($user->lock, 1);
    }

    /** @test */
    function login_with_second_password_should_be_ok()
    {
        $user = $this->user;
        $user->second_password_expired = Carbon::now()->addDays(1)->format('Y-m-d H:i:s');
        $user->second_password = Hash::make('123456');
        $user->save();

        $response = $this->post(route('api.login'), [
            'email' => $user->email,
            'password' => '123456',
            'device_id' => 'random_device_id'
        ]);
        $response->assertOk();
        $response->assertJson(['success' => true, 'data' => [
            'email' => $user->email,
            'device_id' => 'random_device_id'
        ]]);
    }

    /** @test */
    function login_with_expired_second_password_should_return_401()
    {
        $user = $this->user;
        $user->second_password_expired = Carbon::now()->addDays(-1)->format('Y-m-d H:i:s');
        $user->second_password = Hash::make('123456');
        $user->save();

        $response = $this->post(route('api.login'), [
            'email' => $user->email,
            'password' => '123456',
            'device_id' => 'random_device_id'
        ]);
        $response->assertUnauthorized();
        $response->assertJson([
            'success' => false,
            'data' => null,
            'message' => 'Reset password sudah kadaluarsa, silahkan request reset password baru.'
        ]);
    }
}
