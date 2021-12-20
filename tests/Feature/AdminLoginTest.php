<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function redirect_to_home_page_because_not_login()
    {
        $response = $this->get('/');

        //harus redirect ke page login apabila belum login
        $response->assertRedirect('/login');
    }


    /** @test */
    function test_login_success()
    {
        $user = Admin::factory(1)->create();

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $response->assertRedirect('/');
    }
}
