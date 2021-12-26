<?php

namespace Tests\Feature\Api;

use App\Models\Announcement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function get_all_announcements()
    {
        $user = User::factory(1)->create()->first();
        $announcement = Announcement::factory(100)->create()->first();
        $response = $this->actingAs($user, 'api')->get(route('api.announcement'));

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'start_date',
                    'end_date',
                    'description',
                    'title',
                    'attachment'
                ]
            ],
            'success',
        ]);
    }

    /** @test */
    function get_all_announcements_filtered_by_date()
    {
        $user = User::factory(1)->create()->first();
        $announcement = Announcement::factory(100)->create()->first();
        $response = $this->actingAs($user, 'api')->call('GET', route('api.announcement'), [
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->format('Y-m-d'),
        ]);

        //should return data count <= zero, because filtered data return nothing
        $this->assertTrue(count($response->json('data')) <= 0);

        $response = $this->actingAs($user, 'api')->call('GET', route('api.announcement'), [
            'start_date' => Carbon::now()->subDays(20)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
        ]);

        //should return data count more than zero
        $this->assertTrue(count($response->json('data')) > 0);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'start_date',
                    'end_date',
                    'description',
                    'title',
                    'attachment'
                ]
            ],
            'success',
        ]);
    }
}
