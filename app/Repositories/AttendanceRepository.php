<?php

namespace App\Repositories;

use App\Enums\AttendanceType;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Storage;
use Yajra\DataTables\Facades\DataTables;

class AttendanceRepository extends BaseRepository
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
        $attendance->clock_in = Carbon::now();
        $attendance->clock_out = null;
        $attendance->user_id = auth()->user()->id;
        $attendance->latitude = $request->post('latitude');
        $attendance->longtitude = $request->post('longtitude');
        $attendance->location_id = !$request->post('type') ? null : $request->post('location_id');
        $attendance->location_name = $request->post('location_name');
        $attendance->description = $request->post('description');
        $attendance->reason = $request->post('reason');
        $attendance->type = $request->post('type');

        if ($request->file('image')) {
            $request->file('image')->storeAs('attendance/', $request->file('image')->hashName(), ['disk' => 'attendance']);

            $attendance->image = 'private/attendance/' . $request->file('image')->hashName();
        }

        $attendance->save();
        $attendance->refresh();

        return $attendance;
    }

    public function clockOut(FormRequest $request)
    {
        $attendance = new Attendance();
        $attendance->clock_in = null;
        $attendance->clock_out = Carbon::now();
        $attendance->user_id = auth()->user()->id;
        $attendance->latitude = $request->post('latitude');
        $attendance->longtitude = $request->post('longtitude');
        $attendance->location_id = !$request->post('type') ? null : $request->post('location_id');
        $attendance->location_name = $request->post('location_name');
        $attendance->description = $request->post('description');
        $attendance->reason = $request->post('reason');
        $attendance->type = $request->post('type');

        if ($request->file('image')) {
            $request->file('image')->storeAs('attendance/', $request->file('image')->hashName(), ['disk' => 'attendance']);

            $attendance->image = 'private/attendance/' . $request->file('image')->hashName();
        }

        $attendance->save();
        $attendance->refresh();

        return $attendance;
    }



    public function paginate(Request $request)
    {
        $userTable = with(new User)->getTable();
        $attendancesTable = with(new Attendance)->getTable();
        $model = Attendance::select('attendances.*', 'users.nama as user_nama')
            ->join($userTable, $userTable . '.id', '=', $attendancesTable . '.user_id');

        return DataTables::eloquent($model)
            ->filter(function ($attendances) use ($request) {
                $request->get('user_nama') && $attendances->where('users.nama', 'like', "%{$request->get('user_nama')}%");

                $request->get('start_date') && $attendances->where('attendances.clock_in', '>=', $request->get('start_date'));

                $request->get('end_date') && $attendances->where('attendances.clock_in', '<=', $request->get('end_date'));
            })
            ->setTransformer(function ($transform) {
                $data = $transform->toArray();
                $data['type'] = AttendanceType::getKey($data['type']);
                return $data;
            })
            ->toArray();
    }
}
