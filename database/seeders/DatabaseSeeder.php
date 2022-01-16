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
        \App\Models\User::factory(52)->create();
        \App\Models\Announcement::factory(100)->create();
        \App\Models\Location::factory(10)->create();
        DB::beginTransaction();
        for ($i = 0; $i < 100; $i++) {
            \App\Models\Schedule::factory(1)->create();
        }
        for ($i = 0; $i < 100; $i++) {
            \App\Models\Attendance::factory(1)->create();
        }
        DB::commit();
        // \App\Models\Family::factory(100)->create();

    }
}
