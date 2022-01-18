<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Output\ConsoleOutput;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {


        $output = new ConsoleOutput();
        $output->writeln("<fg=green>Create Admin</>: 1 Row");
        \App\Models\Admin::factory(1)->create();

        $output = new ConsoleOutput();
        $output->writeln("<fg=green>Create User</>: 100 Rows");
        \App\Models\User::factory(100)->create();

        $output = new ConsoleOutput();
        $output->writeln("<fg=green>Create Announcement</>: 100 Rows");
        \App\Models\Announcement::factory(100)->create();

        $output = new ConsoleOutput();
        $output->writeln("<fg=green>Create Location</>: 10 Rows");
        \App\Models\Location::factory(10)->create();

        $countCreateShedule = 300;
        $output = new ConsoleOutput();
        $output->writeln("<fg=green>Create Schedule</>: {$countCreateShedule} Rows");
        \App\Models\Schedule::factory($countCreateShedule)->create();

        $output = new ConsoleOutput();
        $output->writeln("<fg=green>Create Attendance</>: " . 6 * $countCreateShedule . " Rows");
        \App\Models\Attendance::factory(6 * $countCreateShedule)->create();
        // \App\Models\Family::factory(100)->create();

    }
}
