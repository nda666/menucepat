<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        return new BaseResource(Setting::all());
    }
}
