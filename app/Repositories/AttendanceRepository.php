<?php

namespace App\Repositories;

use App\Enums\AttendanceType;
use App\Enums\ClockType;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    private function baseAttendance(Request $request)
    {
        $userTable = with(new User)->getTable();
        $attendancesTable = with(new Attendance)->getTable();
        $model = Attendance::select('attendances.*', 'users.nama as user_nama', 'users.nik as nik')
            ->join($userTable, $userTable . '.id', '=', $attendancesTable . '.user_id');

        return $model;
    }

    private function filterPaginated(Builder $attendances, Request $request)
    {
        $request->get('nameOrNIK') && $attendances->where(function ($multiWhere) use ($request) {
            $multiWhere->where('users.nama', 'like', "%{$request->get('nameOrNIK')}%");
            $multiWhere->orWhere('users.nik', $request->get('nameOrNIK'));
        });

        $request->get('attendanceType') != '' && $attendances->where('attendances.type', $request->get('attendanceType'));

        $request->get('start_date') && $attendances->where('attendances.check_clock', '>=', $request->get('start_date'));

        $request->get('end_date') && $attendances->where('attendances.check_clock', '<=', $request->get('end_date'));

        return $attendances;
    }

    public function paginate(Request $request)
    {

        $model = $this->baseAttendance($request);

        return DataTables::eloquent($model)
            ->filter(function ($attendances) use ($request) {
                $this->filterPaginated($attendances, $request);
            })
            ->setTransformer(function ($transform) {
                $data = $transform->toArray();
                $data['type'] = AttendanceType::getKey($data['type']);
                $data['clock_type'] = ClockType::getKey($data['clock_type']);
                return $data;
            })
            ->toArray();
    }

    public function makeExcel(Request $request)
    {
        $oAttendance = $this->baseAttendance($request);
        $attendances = $this->filterPaginated($oAttendance, $request)
            ->orderBy('users.nama', 'asc')
            ->orderBy('attendances.check_clock', 'asc')
            ->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $sheet = $this->createExcelHeaderCell($sheet);

        foreach ($attendances as $attendance) {
        }

        $writer = new Xls($spreadsheet);
        $response =  new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="ExportScan.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;
    }

    private function createExcelHeaderCell(Worksheet $sheet)
    {
        $sheet->mergeCells('A3:A4');
        $sheet->setCellValue('A3', 'Photo');

        $sheet->mergeCells('B3:B4');
        $sheet->setCellValue('B3', 'NIK');

        $sheet->mergeCells('C3:C4');
        $sheet->setCellValue('C3', 'NAMA');

        $sheet->mergeCells('D3:E3');
        $sheet->setCellValue('D3', 'Jadwal Detail');
        $sheet->setCellValue('D4', 'Dutty On');
        $sheet->setCellValue('E4', 'Dutty Off');

        $sheet->mergeCells('F3:G3');
        $sheet->setCellValue('F3', 'Aktual Detail');
        $sheet->setCellValue('F4', 'Datetime');
        $sheet->setCellValue('G4', 'Tipe Clock');

        $sheet->mergeCells('I3:I4');
        $sheet->setCellValue('I3', 'Tipe Attendance');
    }
}
