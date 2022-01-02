<?php

namespace App\Repositories;

use App\Enums\AttendanceType;
use App\Enums\ClockType;
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
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param int $clocktype ClockType Instance
     * 
     * @return App/Models/Attendance
     */
    public function checkClock(FormRequest $request, int $clockType)
    {
        $attendance = new Attendance();
        $attendance->check_clock = Carbon::now();
        $attendance->clock_type = $clockType;
        $attendance->user_id = auth()->user()->id;
        $attendance->latitude = $request->post('latitude');
        $attendance->longtitude = $request->post('longtitude');
        $attendance->location_id = $request->post('type') != AttendanceType::LIVE ? null : $request->post('location_id');
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

    /**
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * 
     * @return App/Models/Attendance
     */
    public function clockIn(FormRequest $request)
    {
        return $this->checkClock($request, ClockType::IN);
    }

    /**
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * 
     * @return App/Models/Attendance
     */
    public function clockOut(FormRequest $request)
    {
        return $this->checkClock($request, ClockType::OUT);
    }

    public function createFromAdmin(Request $request)
    {
        $attendance = new Attendance();
        $attendance->check_clock = $request->post('check_clock');
        $attendance->clock_type = $request->post('clock_type');
        $attendance->user_id = $request->post('user_id');
        $attendance->latitude = $request->post('latitude');
        $attendance->longtitude = $request->post('longtitude');
        $attendance->location_id = $request->post('location_id');
        $attendance->location_name = $request->post('location_name');
        $attendance->description = $request->post('description');
        $attendance->reason = $request->post('reason');
        $attendance->type = AttendanceType::SYSTEM;
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
        $model = Attendance::select('attendances.*', 'users.nama as user_nama', 'users.nik as nik')
            ->join($userTable, $userTable . '.id', '=', $attendancesTable . '.user_id');

        return DataTables::eloquent($model)
            ->filter(function ($attendances) use ($request) {
                $request->get('nameOrNIK') && $attendances->where(function ($multiWhere) use ($request) {
                    $multiWhere->where('users.nama', 'like', "%{$request->get('nameOrNIK')}%");
                    $multiWhere->orWhere('users.nik', $request->get('nameOrNIK'));
                });



                $request->get('start_date') && $attendances->where('attendances.check_clock', '>=', $request->get('start_date'));

                $request->get('end_date') && $attendances->where('attendances.check_clock', '<=', $request->get('end_date'));
            })
            ->setTransformer(function ($transform) {
                $data = $transform->toArray();
                $data['type'] = AttendanceType::getKey($data['type']);
                $data['clock_type'] = ClockType::getKey($data['clock_type']);
                return $data;
            })
            ->toArray();
    }
}
