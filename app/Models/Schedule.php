<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    public $dates = ['created_at', 'updated_at', 'duty_on', 'duty_off'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
