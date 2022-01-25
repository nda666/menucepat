<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ClockInRequest;
use App\Http\Requests\Api\ClockOutRequest;
use App\Http\Resources\Api\AttendanceResource;
use App\Http\Resources\BaseResource;
use App\Models\Attendance;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;
use Storage;

class AttendanceController extends Controller
{
    /**
     * @var \App\Repositories\AttendanceRepository
     */
    private $attendanceRepo;

    public function __construct(AttendanceRepository $attendanceRepo)
    {
        $this->attendanceRepo = $attendanceRepo;
    }

    public function index(Request $request)
    {
        $attendances = $this->attendanceRepo->findAll($request);
        return AttendanceResource::collection($attendances);
    }

    public function currentAttendance()
    {
        $attendance = $this->attendanceRepo->currentAttendance(auth()->user()->id);
        return new BaseResource($attendance);
    }

    public function clockIn(ClockInRequest $clockInRequest)
    {
        $attendance = $this->attendanceRepo->clockIn($clockInRequest);
        return $this->checkClockResponse($attendance);
    }

    public function clockOut(ClockOutRequest $clockOutRequest)
    {
        $attendance = $this->attendanceRepo->clockOut($clockOutRequest);
        return $this->checkClockResponse($attendance);
    }

    private function checkClockResponse($attendance)
    {
        return $attendance ?
            (new AttendanceResource($attendance['attendance']))->additional(['message' => $attendance['message']]) : (new AttendanceResource(null, false))->additional(['message' => 'Terjadi kesalahan, check clock gagal disimpan']);
    }

    public function attendanceImage(Request $request)
    {
        return Storage::response($request->get('path'));
    }
}
