<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{

    public static $createdData = [];



    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $date = $this->faker->dateTimeBetween('-18 days')->format('Y-m-d');

        $visitList = [
            '10:00:00' => ' 11:00:00',
            '12:00:00' => '13:00:00',
        ];

        $exist = true;
        $user_id = null;
        $notIn = [];
        $type = 0;
        $clockType = 0;
        $checkClock = null;

        $schedule = Schedule::all()->toArray();
        $scheduleIndex = 0;
        $visitIndex = 0;
        $posibleValue = [];
        $posibleValueIndex = 0;
        $value = [];

        while ($exist) {

            $posibleValue = [];
            $createdData = self::$createdData;
            if (isset($createdData[$schedule[$scheduleIndex]['id']]) && count($createdData[$schedule[$scheduleIndex]['id']]) >= 6) {
                $scheduleIndex++;
                continue;
            }

            $parse = Carbon::parse($schedule[$scheduleIndex]['duty_on']);
            $checkClockIn = $this->faker->dateTimeBetween(
                $parse->subHour(1)->format('Y-m-d H:i:s'),
                $parse->addHour(1)->format('Y-m-d H:i:s')
            )->format('Y-m-d H:i:s');

            $parseOut = Carbon::parse($schedule[$scheduleIndex]['duty_off']);
            $checkClockOut = $this->faker->dateTimeBetween(
                $parseOut->subHour(1)->format('Y-m-d H:i:s'),
                $parseOut->addHour(1)->format('Y-m-d H:i:s')
            )->format('Y-m-d H:i:s');

            $posibleValue = [[
                'schedule_id' => $schedule[$scheduleIndex]['id'],
                'user_id' => $schedule[$scheduleIndex]['user_id'],
                'type' => 0,
                'clock_type' => 0,
                'check_clock' => $checkClockIn,
                'created_at' => $checkClockIn,
            ], [
                'schedule_id' => $schedule[$scheduleIndex]['id'],
                'user_id' => $schedule[$scheduleIndex]['user_id'],
                'type' => 0,
                'clock_type' => 1,
                'check_clock' => $checkClockOut,
                'created_at' => $checkClockIn,
            ]];

            foreach ($visitList as $visitIn => $visitOut) {
                $posibleValue[] = [
                    'schedule_id' => $schedule[$scheduleIndex]['id'],
                    'user_id' => $schedule[$scheduleIndex]['user_id'],
                    'type' => 1,
                    'clock_type' => 0,
                    'check_clock' => $parse->format('Y-m-d ') . $visitIn,
                    'created_at' => $parse->format('Y-m-d ') . $visitIn,
                ];
                $posibleValue[] = [
                    'schedule_id' => $schedule[$scheduleIndex]['id'],
                    'user_id' => $schedule[$scheduleIndex]['user_id'],
                    'type' => 1,
                    'clock_type' => 1,
                    'check_clock' => $parse->format('Y-m-d ') . $visitOut,
                    'created_at' => $parse->format('Y-m-d ') . $visitOut,
                ];
            }

            foreach ($posibleValue as $k => $pos) {

                if (!isset($createdData[$schedule[$scheduleIndex]['id']][$k])) {
                    $value = $pos;
                    $value['latitude'] = $this->faker->latitude();
                    $value['longtitude'] = $this->faker->longitude();
                    $value['location_id'] = Location::all()->random(1)->first()->id;
                    $value['location_name'] = 'Test Lokasi';
                    $value['image'] = 'private/attendance/zgKeuF8w18FdLP5KosT9SlEOgWuwhbMHLo6A5PNs.png';
                    $value['description'] = $this->faker->words(3, true);
                    $exist = false;
                    $posibleValueIndex = $k;
                    break 2;
                }
            }

            // exhausted
            if ($scheduleIndex == count($schedule) - 1) {
                return false;
            }
        }

        self::$createdData[$schedule[$scheduleIndex]['id']][$posibleValueIndex] = true;

        return $value;
    }
}
