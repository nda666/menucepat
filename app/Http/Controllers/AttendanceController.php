<?php

namespace App\Http\Controllers;

use App\Enums\ClockType;
use App\Http\Requests\AttendanceRequest;
use App\Http\Resources\BaseResource;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private $attendanceRepo;

    public function __construct(AttendanceRepository $attendanceRepo)
    {
        $this->attendanceRepo = $attendanceRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('attendance.index');
    }

    public function table(Request $request)
    {
        return response()->json($this->attendanceRepo->paginate($request));
    }

    public function excel(Request $request)
    {
        return $this->attendanceRepo->makeExcel($request)->send();
    }

    /**
     * Store Attendance
     *
     * @param   AttendanceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttendanceRequest $request)
    {
        return response()->json($this->attendanceRepo->createFromAdmin($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->attendanceRepo->find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->attendanceRepo->update($request, $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $attendance = $this->attendanceRepo->update($request, $id);
        return new BaseResource($attendance);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->attendanceRepo->delete($id);
    }
}
