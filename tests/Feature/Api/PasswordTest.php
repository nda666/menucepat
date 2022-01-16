<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function change_password()
    {
        $user = User::factory(1)->create()->first();
        $user->email = 'adhabakhtiar@gmail.com';
        $user->save();
        $response = $this->actingAs($user, 'api')->post(route('api.resetPassword'), [
            'email' => $user->email,
        ]);

        $response->assertJson([
            'success' => true,
            'message' => 'Kami sudah mengirim surel yang berisi password baru untuk Anda'
        ]);
    }
}
