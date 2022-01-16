<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $exist = true;
        $user = null;
        $duty_on = '';
        $duty_off = '';
        while ($exist) {
            $user = User::all()->random()->first();
            $date = rand(1, 31);
            $date = strlen($date) == 1 ? '0' . $date : $date;
            $duty_on = '2021-01-' . $date . ' 07:00:00';
            $duty_off = '2021-01-' . $date . ' 17:00:00';

            $scheduleExist = Schedule::where('user_id', $user->id)
                ->whereDate('duty_on', '2021-01-' . $date)
                ->first();
            $exist = $scheduleExist;
        }

        return [
            'code' => $this->faker->regexify('[A-Z0-9]{2}'),
            'user_id' => $user->id,
            'duty_on' =>  $duty_on,
            'duty_off' => $duty_off
        ];
    }
}
