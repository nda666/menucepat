<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function attendance_table_test()
    {
        $user = Admin::factory(1)->create()->first();
        $employee = User::factory(20)->create();
        $location = Location::factory(5)->create();
        $attendance = Attendance::factory(100)->create();
        $response = $this->actingAs($user, 'admin')
            ->get('/attendance/table');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_nama',
                    'check_clock',
                    'clock_type',
                    'location_id',
                    'latitude',
                    'longtitude',
                    'reason',
                    'description',
                    'location_name',
                    'type',
                    'created_at',
                    'updated_at',
                ],
            ],
            'recordsTotal',
            'recordsFiltered',
        ]);
    }

    // /** @test */
    // function show_user_test()
    // {
    //     $admin = Admin::factory(1)->create()->first();
    //     $user = User::factory(2)->create()->first();

    //     $response = $this->actingAs($admin, 'admin')
    //         ->get("user/notfoundtest");
    //     $response->assertNotFound();

    //     $response = $this->actingAs($admin, 'admin')
    //         ->get("user/{$user->id}");

    //     $response->assertOk();
    //     $response->assertJsonStructure([

    //         'email',
    //         'tgl_lahir',
    //         'kota_lahir',
    //         'divisi',
    //         'subdivisi',
    //         'company',
    //         'department',
    //         'jabatan',
    //         'lokasi',
    //         'bagian',
    //         'sex',
    //         'blood',
    //         'alamat',
    //         'nik',
    //         'whatsapp',
    //     ]);
    // }
}
