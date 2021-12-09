<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AttendaceRepository extends BaseRepository
{
    public function __construct(Attendance $model)
    {
        parent::__construct($model);
    }

    public function currentAttendance($userId)
    {
        $attendance = Attendance::where('user_id', $userId)
            ->where('clock_in', '<=', Carbon::today()->format('Y-m-d 23:59:59'))
            ->where('clock_in', '>=', Carbon::today()->format('Y-m-d 00:00:00'))
            ->get();

        return $attendance;
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Attendance
     */
    public function clockIn(FormRequest $request)
    {
        $attendance = new Attendance();

        if ($request->post('clock_in')) {
            $attendance->clock_in = Carbon::now();
        }

        $attendance->user_id = auth()->user()->id;
        $attendance->latitude = $request->post('latitude');
        $attendance->longtitude = $request->post('longtitude');
        $attendance->location_id = !$request->post('type') ? null : $request->post('location_id');
        $attendance->location_name = $request->post('location_name');
        $attendance->image = $request->post('image');
        $attendance->description = $request->post('description');
        $attendance->reason = $request->post('reason');
        $attendance->type = $request->post('type');
        $attendance->save();

        return $attendance;
    }

    public function clockOut($attendance)
    {
        $attendance->clock_out = Carbon::now();
        $attendance->save();
        return $attendance;
    }
}
