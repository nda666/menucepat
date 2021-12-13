<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ClockInRequest;
use App\Http\Requests\Api\ClockOutRequest;
use App\Http\Resources\BaseResource;
use App\Models\Attendance;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;
use Storage;

class AttendanceController extends Controller
{
    private $attendaceRepo;

    public function __construct(AttendanceRepository $attendaceRepo)
    {
        $this->attendaceRepo = $attendaceRepo;
    }

    public function currentAttendance()
    {
        $attendance = $this->attendaceRepo->currentAttendance(auth()->user()->id);

        return new BaseResource($attendance);
    }

    /** */
    public function clockIn(ClockInRequest $clockInRequest)
    {
        $attendance = $this->attendaceRepo->clockIn($clockInRequest);

        return $attendance ? new BaseResource($attendance) : response()->json(['message' => 'Absensi gagal disimpan', 501]);
    }

    public function clockOut(ClockOutRequest $clockOutRequest)
    {
        $attendance = $this->attendaceRepo->clockOut($clockOutRequest);

        return $attendance ? new BaseResource($attendance) : response()->json(['message' => 'Absensi gagal disimpan', 501]);
    }

    public function attendanceImage(Request $request)
    {

        $attendance = Attendance::where('image', $request->get('path'))->first();
        // if (auth()->user()->can('view', $attendance)) {
        return Storage::response($request->get('path'));
        // } else {
        //     return 'Nope, sorry bro, access denied!';
        // }
    }
}
