<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FamilyController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Resources\Api\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return response()->json(['oke']);
});

Route::middleware('api')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/reset-password', [PasswordController::class, 'resetPassword']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('/profile', function () {
        $result = new UserResource(auth()->user(), null);
        return $result->jsonSerialize();
    })->name('api.profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('api.profile.save');
    Route::post('/password', [ProfileController::class, 'password'])->name('api.password.save');


    Route::get('/users', [UserController::class, 'index'])->name('api.users');
    Route::get('/family', [FamilyController::class, 'index'])->name('api.family');

    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('api.clockIn');
    Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('api.clockOut');
    Route::get('/current-attendance', [AttendanceController::class, 'currentAttendance'])->name('api.currentAttendance');
    Route::get('/attendance/image', [AttendanceController::class, 'attendanceImage'])->name('api.attendanceImage');

    Route::get('location', [LocationController::class, 'index'])->name('api.location');
    Route::get('setting', [SettingController::class, 'index'])->name('api.setting');

    Route::get('announcement', [AnnouncementController::class, 'index'])->name('api.announcement');
});



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
