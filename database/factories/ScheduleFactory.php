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
        $i = 1;
        $user = User::all()->random(1)->first();
        while ($exist) {
            if ($i == 31) {
                $i = 1;
            }


            $date = strlen($i) == 1 ? '0' . $i : $i;
            $duty_on = '2021-01-' . $date . ' 07:00:00';
            $duty_off = '2021-01-' . $date . ' 17:00:00';

            $scheduleExist = Schedule::where('user_id', $user->id)
                ->where('duty_on', $duty_on)
                ->first();
            $exist = $scheduleExist;
            $i++;
        }

        return [
            'code' => $this->faker->regexify('[A-Z0-9]{2}'),
            'user_id' => $user->id,
            'duty_on' =>  $duty_on,
            'duty_off' => $duty_off
        ];
    }
}
