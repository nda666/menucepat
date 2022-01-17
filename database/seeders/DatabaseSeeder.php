<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Admin::factory(1)->create();
        \App\Models\User::factory(100)->create();
        \App\Models\Announcement::factory(100)->create();
        \App\Models\Location::factory(10)->create();
        for ($i = 0; $i < 3000; $i++) {
            \App\Models\Schedule::factory(1)->create();
        }
        for ($i = 0; $i < 3000; $i++) {
            \App\Models\Attendance::factory(1)->create();
        }
        // \App\Models\Family::factory(100)->create();

    }
}
