<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public static $createdData = [];
    public $duty_on = null;
    public static $countCreated = 0;
    public static $indexUser = 0;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if (!$this->duty_on) {
            $this->duty_on = Carbon::now();
        }
        $exist = true;
        $user = null;
        $duty_on = '';
        $duty_off = '';
        $userId = Schedule::all()->pluck('user_id')->toArray();
        $user = $userId ?
            User::whereNotIn('id', $userId)->get()->toArray() :
            User::all()->toArray();
        $userId = $user[0]['id'];
        while ($exist) {
            if (self::$countCreated >= 30) {
                self::$countCreated = 0;
                self::$indexUser++;
                $this->duty_on = Carbon::now();
            }
            $userId = $user[self::$indexUser]['id'];

            // $date = strlen($i) == 1 ? '0' . $i : $i;
            $duty_on =  $this->duty_on->format('Y-m-d H:i:s');
            $duty_off =  $this->duty_on->format('Y-m-d 17:i:s');

            // $scheduleExist = Schedule::where('user_id', $userId)
            //     ->where('duty_on', $duty_on)
            //     ->first();

            $exist = isset(self::$createdData[$userId][$duty_on]);
            $this->duty_on->addDay();
        }
        self::$countCreated++;
        $insert = [
            'code' => $this->faker->regexify('[A-Z0-9]{2}'),
            'user_id' => $userId,
            'duty_on' =>  $duty_on,
            'duty_off' => $duty_off
        ];

        self::$createdData[$userId][$duty_on] = true;

        return $insert;
    }
}
