<?php

namespace App\Models;

use App\Enums\AttendanceType;
use App\Enums\ClockType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, CastsEnums;

    public $dates = ['created_at', 'updated_at', 'check_clock'];

    protected $casts = [
        'type' => AttendanceType::class,
        'clock_type' => ClockType::class
    ];

    public function getImageAttribute($image)
    {
        return route('api.attendanceImage', ['path' => $image]);
    }
}
