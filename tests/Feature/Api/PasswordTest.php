<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PasswordTest extends TestCase
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
    function reset_password()
    {
        $user = $this->user;
        $response = $this->post(route('api.resetPassword'), [
            'email' => $user->email,
        ]);
        $response->assertJson([
            'success' => true,
            'message' => 'Kami sudah mengirim surel yang berisi password baru untuk Anda'
        ]);
    }
}
