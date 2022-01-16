<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-18 days')->format('Y-m-d');
        $clockIn = $this->faker->dateTimeBetween('2021-01-01 07:00:00', '2021-01-01 08:00:00')->format('H:i:s');
        $clockOut = $this->faker->dateTimeBetween('2021-01-01 17:00:00', '2021-01-01 18:00:00')->format('H:i:s');
        $exist = true;
        $user_id = null;
        $attendances = Attendance::all();
        $notIn = [];
        foreach ($attendances as $attendance) {
            $notIn[] = $attendance->schedule_id;
        }

        while ($exist) {
            $schedule = null;
            if ($notIn) {
                $schedule = Schedule::whereNotIn('id', $notIn)->first();
            } else {
                $schedule = Schedule::all()->random(1)->first();
            }
            $attendanceExist = Attendance::where('user_id', $schedule->user_id)
                ->where('schedule_id', $schedule->id)
                ->first();
            $exist = $attendanceExist;
        }
        return [
            'schedule_id' => $schedule ? $schedule->id : null,
            'latitude' => $this->faker->latitude(),
            'longtitude' => $this->faker->longitude(),
            'user_id' => $schedule->user_id,
            'check_clock' => $date . ' ' . $clockIn,
            'clock_type' => rand(0, 1),
            'location_id' => Location::all()->random(1)->first()->id,
            'location_name' => 'Test Lokasi',
            'image' => 'private/attendance/zgKeuF8w18FdLP5KosT9SlEOgWuwhbMHLo6A5PNs.png',
            'description' => $this->faker->words(3, true),
            'reason' => $this->faker->words(3, true),
            'type' => rand(0, 1),

        ];
    }
}
