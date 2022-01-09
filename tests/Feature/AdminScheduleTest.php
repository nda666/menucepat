<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminScheduleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function schedule_table_test()
    {
        $user = Admin::factory(1)->create()->first();
        $response = $this->actingAs($user, 'admin')
            ->get('/schedule/table');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_nama',
                    'duty_on',
                    'duty_off',
                    'created_at',
                    'updated_at',
                ],
            ],
            'recordsTotal',
            'recordsFiltered',
        ]);
    }

    /** @test */
    function schedule_create_test()
    {
        $admin = Admin::factory(1)->create()->first();
        $user = User::factory(1)->create()->first();
        $response = $this->actingAs($user, 'admin')
            ->post('schedule', [
                "user_id" => $user->id,
                "code" => 'CN',
                "duty_on" => "2022-01-09 08:00",
                "duty_off" => "2022-01-09 16:00"
            ]);

        // should return 201
        $response->assertStatus(201);
        // Should return response like this
        $response->assertJson([
            'success' => true,
            'data' => [
                "user_id" => $user->id,
                "code" => 'CN',
                "duty_on" => "2022-01-09 08:00",
                "duty_off" => "2022-01-09 16:00"
            ],
        ]);
    }
}
