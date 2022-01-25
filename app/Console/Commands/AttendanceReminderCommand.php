<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AttendanceReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iris:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to employees';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $schedules = Schedule::with(['user'])
            ->whereDate('duty_on', Carbon::now()->format('Y-m-d'))
            ->where('reminder_sent', 0)
            ->get();

        foreach ($schedules as $schedule) {
            // $schedule->reminder_sent = 1;
            $schedule->save();
            $this->info('Reminder sent to ' . $schedule->user->name);
        }
    }
}
