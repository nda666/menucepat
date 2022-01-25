<?php

namespace Tests\Feature\Api;

use App\Models\Attendance;
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
        $schedule->duty_on = Carbon::now()->format('Y-m-d H:i:s');
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

        $attendance = Attendance::find(2);
        //    print_r($response->json());
        $response->assertJson([
            'data' => [
                'type' => 0,
                'latitude' => 'Test latitude',
                'longtitude' => 'Test longtitude',
                'location_id' => $location->id
            ],
            'success' => true,
            'message' => __('attendance.success')
        ]);
    }

    /** @test */
    function check_in_on_last_second_of_duty_on_should_be_ok()
    {
        $user = User::factory(1)->create()->first();
        $location = Location::factory(1)->create()->first();
        $schedule = Schedule::factory(1)->make()->first();

        // make duty_on to be last second of duty_on
        $schedule->duty_on = Carbon::now()->subSeconds(58)->format('Y-m-d H:i:s');
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

        $attendance = Attendance::find(2);
        //    print_r($response->json());
        $response->assertJson([
            'data' => [
                'type' => 0,
                'latitude' => 'Test latitude',
                'longtitude' => 'Test longtitude',
                'location_id' => $location->id
            ],
            'success' => true,
            'message' => __('attendance.success')
        ]);
    }

    /** @test */
    function check_in_without_schedule_should_be_ok()
    {
        $user = User::factory(1)->create()->first();
        $location = Location::factory(1)->create()->first();

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
            'message' => __('attendance.success')
        ]);
    }


    /** @test */
    function late_check_in_user_should_get_late_message()
    {
        $user = User::factory(1)->create()->first();
        $location = Location::factory(1)->create()->first();
        $schedule = Schedule::factory(1)->make()->first();
        $schedule->duty_on = Carbon::now()->subHour(1)->format('Y-m-d 08:00:00');
        $schedule->duty_off = Carbon::now()->addHour(1)->format('Y-m-d 18:00:00');
        $schedule->user_id = $user->id;
        $schedule->code = 'CO';
        $schedule->save();

        $response = $this->actingAs($user, 'api')->post(route('api.clockIn'), [
            'type' => 0,
            'latitude' => 'Test latitude',
            'longtitude' => 'Test longtitude',
            'location_id' => $location->id
        ]);

        $response->assertJson([
            'data' => [
                'type' => 0,
                'latitude' => 'Test latitude',
                'longtitude' => 'Test longtitude',
                'location_id' => $location->id
            ],
            'success' => true,
            'message' => __('attendance.late')
        ]);
    }

    /** @test */
    function early_check_out_user_should_get_early_message()
    {
        $user = User::factory(1)->create()->first();
        $location = Location::factory(1)->create()->first();
        $schedule = Schedule::factory(1)->make()->first();
        $schedule->duty_on = Carbon::now()->format('Y-m-d 08:00:00');
        $schedule->duty_off = Carbon::now()->addHour(1)->format('Y-m-d H:i:s');
        $schedule->user_id = $user->id;
        $schedule->code = 'CO';
        $schedule->save();

        $response = $this->actingAs($user, 'api')->post(route('api.clockOut'), [
            'type' => 0,
            'latitude' => 'Test latitude',
            'longtitude' => 'Test longtitude',
            'location_id' => $location->id
        ]);

        $response->assertJson([
            'data' => [
                'type' => 0,
                'latitude' => 'Test latitude',
                'longtitude' => 'Test longtitude',
                'location_id' => $location->id
            ],
            'success' => true,
            'message' => __('attendance.early')
        ]);
    }
}
