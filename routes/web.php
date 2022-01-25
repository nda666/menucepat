<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::get('opti', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo 'ok';
});

Route::get('generate', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo 'ok';
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('dashboard');

    /** User Router */
    Route::get('/user/table', [UserController::class, 'table'])->name('user.table');
    Route::get('/user/select2', [UserController::class, 'select2'])->name('user.select2');
    Route::post('/user/unlock/{user}', [UserController::class, 'unlock'])->name('user.unlock');
    /**
     * Route resource harus paling bawah dalam prefix
     */
    Route::resource('user', UserController::class);


    Route::get('/location/table', [LocationController::class, 'table'])->name('location.table');
    Route::resource('location', LocationController::class);

    Route::get('/family/table', [FamilyController::class, 'table'])->name('family.table');
    Route::resource('family', FamilyController::class);

    Route::get('/announcement/table', [AnnouncementController::class, 'table'])->name('announcement.table');
    Route::resource('announcement', AnnouncementController::class);

    Route::get('/schedule/table', [ScheduleController::class, 'table'])->name('schedule.table');
    Route::resource('schedule', ScheduleController::class);

    Route::get('/attendance/table', [AttendanceController::class, 'table'])->name('attendance.table');
    Route::get('/attendance/excel', [AttendanceController::class, 'excel'])->name('attendance.excel');
    Route::get('/attendance/image', [AttendanceController::class, 'attendanceImage'])
        ->name('attendance.image');
    Route::resource('attendance', AttendanceController::class);

    Route::get('/setting/table', [SettingController::class, 'table'])->name('setting.table');
    Route::resource('setting', SettingController::class);
});
