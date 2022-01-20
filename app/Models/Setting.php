<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // protected function serializeDate(DateTimeInterface $date)
    // {
    //     return $date->format('Y-m-d H:i:s');
    // }

    protected $fillable = [
        'key',
        'value'
    ];

    /**
     * Get setting stored in database table settings
     *
     * @param mixed $key The key of the setting
     * @param mixed $default The default value to return if the setting is not found
     * @return string
     */
    static function get($key, $default = null)
    {

        $model = (new static)::where('key', $key)
            ->first();
        if (empty($model)) {
            if (empty($default)) {
                //Throw an exception, you cannot resume without the setting.
                throw new \Exception('Cannot find setting: ' . $key);
            } else {
                return $default;
            }
        } else {
            return $model->value;
        }
    }

    /**
     * Set setting stored in database table settings
     *
     * @param string $key The key of the setting
     * @param string $value The value of the setting
     * @return \App\Models\Setting
     */
    static function set(string $key, $value)
    {
        $static = (new static);

        $model = $static::where('key', $key)->first();

        if (empty($model)) {
            $model = $static::create([
                'key' => $key,
                'value' => $value
            ]);
        } else {
            $model->update(compact('value'));
        }

        return $model->value;
    }
}
