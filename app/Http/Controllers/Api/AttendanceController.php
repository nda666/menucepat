<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ClockInRequest;
use App\Http\Resources\BaseResource;
use App\Models\Attendance;
use App\Repositories\AttendaceRepository;

class AttendanceController extends Controller
{
    private $attendaceRepo;

    public function __construct(AttendaceRepository $attendaceRepo)
    {
        $this->attendaceRepo = $attendaceRepo;
    }

    public function currentAttendance()
    {
        $attendance = $this->attendaceRepo->currentAttendance(auth()->user()->id);

        return new BaseResource($attendance);
    }

    /** */
    public function clockIn(ClockInRequest $attendanceRequest)
    {
        $attendance = $this->attendaceRepo->clockIn($attendanceRequest);

        return $attendance ? new BaseResource($attendance) : response()->json(['message' => 'Absensi gagal disimpan', 501]);
    }

    public function clockOut(Attendance $attendance)
    {
        #extra security check if auth.user.id = attendance.user_id
        $this->authorize('update', $attendance);

        $attendance = $this->attendaceRepo->clockOut($attendance);

        return $attendance ? new BaseResource($attendance) : response()->json(['message' => 'Absensi gagal disimpan', 501]);
    }
}
