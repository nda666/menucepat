<?php

namespace App\Models;

use App\Enums\SexType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $casts = [
        'sex' => SexType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
