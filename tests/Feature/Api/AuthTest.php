<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function login_without_device_id_for_first_time_should_save_user_device_id()
    {
        $user = User::factory(1)->create()->first();
        $response = $this->post(route('api.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_id' => 'random_device_id'
        ]);

        // Log::error($response->json());
        $response->assertJson([
            'data' => [
                'email' => $user->email,
                'device_id' => 'random_device_id',
            ],
            'success' => true,
        ]);
    }
}
