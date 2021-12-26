<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function change_profile_test()
    {
        $user = User::factory(10)->create()->first();
        $response = $this->actingAs($user, 'api')->post(route('api.profile.save'), [
            'email' => 'random_email@gmail.com',
            'whatsapp' => '085100825543',
            'alamat' => 'Test Alamat',
            'nama' => 'Change Nama'
        ]);

        // Log::error($response->json());
        $response->assertJson([
            'data' => [
                'email' => 'random_email@gmail.com',
                'whatsapp' => '085100825543',
                'alamat' => 'Test Alamat',
                'nama' => 'Change Nama'
            ],
            'success' => true,
        ]);
    }


    /** @test */
    function change_email_with_current_email_should_be_ok()
    {
        $user = User::factory(10)->create()->first();
        $response = $this->actingAs($user, 'api')->post(route('api.profile.save'), [
            'email' => $user->email,
            'whatsapp' => '085100825543',
            'alamat' => 'Test Alamat',
            'nama' => 'Change Nama'
        ]);

        // Log::error($response->json());
        $response->assertJson([
            'data' => [
                'email' => $user->email,
                'whatsapp' => '085100825543',
                'alamat' => 'Test Alamat',
                'nama' => 'Change Nama'
            ],
            'success' => true,
        ]);
    }

    /** @test */
    function email_already_used_test()
    {
        $users = User::factory(10)->create();
        $user = $users->first();
        $response = $this->actingAs($user, 'api')->post(route('api.profile.save'), [
            'email' => $users->last()->email,
            'whatsapp' => '085100825543',
            'alamat' => 'Test Alamat',
            'nama' => 'Change Nama'
        ]);

        //should return entity error
        $response->assertStatus(422);

        //should return json with message and array of errors
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => []
        ]);
    }
}
