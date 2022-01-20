<?php

namespace Tests\Feature\Api;

use App\Models\Location;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function check_in()
    {
        $user = User::factory(1)->create()->first();
        $location = Location::factory(1)->create()->first();
        $schedule = Schedule::factory(1)->make()->first();
        $schedule->duty_on = Carbon::now()->format('Y-m-d 08:00:00');
        $schedule->duty_off = Carbon::now()->format('Y-m-d 18:00:00');
        $schedule->user_id = $user->id;
        $schedule->code = 'CO';
        $schedule->save();

        $response = $this->actingAs($user, 'api')->post(route('api.clockIn'), [
            'type' => 0,
            'latitude' => 'Test latitude',
            'longtitude' => 'Test longtitude',
            'location_id' => $location->id
        ]);
        //    print_r($response->json());
        $response->assertJson([
            'data' => [
                'type' => 0,
                'latitude' => 'Test latitude',
                'longtitude' => 'Test longtitude',
                'location_id' => $location->id
            ],
            'success' => true,
        ]);
    }
}