<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\ProfileController;
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
    });
    Route::post('/profile', [ProfileController::class, 'update'])->name('api.profile');
    Route::post('/password', [ProfileController::class, 'password'])->name('api.password');

    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('api.clockIn');
    Route::post('/clockout', [ProfileController::class, 'clockOut'])->name('api.clockOut');
    Route::get('/current-attendance', [ProfileController::class, 'currentAttendance'])->name('api.currentAttendance');
});



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
