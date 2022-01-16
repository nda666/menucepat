<?php

namespace Database\Seeders;

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
        \App\Models\User::factory(52)->create();
        \App\Models\Announcement::factory(100)->create();
        \App\Models\Location::factory(10)->create();
        \App\Models\Schedule::factory(3000)->create();
        // \App\Models\Family::factory(100)->create();
        \App\Models\Attendance::factory(100)->create();
    }
}
