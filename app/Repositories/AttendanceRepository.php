<?php

namespace App\Repositories;

use App\Enums\AttendanceType;
use App\Enums\ClockType;
use App\Enums\SexType;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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
        $locationTable = with(new Location)->getTable();
        $model = Attendance::select('attendances.*', $userTable . '.nama as user_nama', $userTable . '.nik as nik', $userTable . '.avatar as user_image', $userTable . '.sex')

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


        $this->createExcelHeaderCell($sheet);
        $row = 5;
        foreach ($attendances as $attendance) {
            dd(Storage::url($attendance->user_image));
            $defaultImage = 'public/images/avatar-m.png';
            if (SexType::getKey($attendance->sex) == 'FEMALE') {
                $defaultImage = 'public/images/avatar-f.png';
            }
            $this->createExcelImageCell($sheet, $attendance->user_image ? $attendance->user_image : $defaultImage, 'A' . $row);
            $sheet->getRowDimension($row)->setRowHeight(50);
            $sheet->setCellValue('B' . $row, $attendance->nik);
            $sheet->setCellValue('C' . $row, $attendance->user_nama);
            $sheet->setCellValue('G' . $row, $attendance->check_clock);
            $sheet->setCellValue('H' . $row, $attendance->clock_type->key);
            $sheet->setCellValue('I' . $row, $attendance->type->key);
            $sheet->setCellValue('J' . $row, $attendance->description);
            $this->createExcelImageCell($sheet, $attendance->getRawOriginal('image'), 'K' . $row);
            $sheet->setCellValue('L' . $row, $attendance->location_name);
            $sheet->setCellValue('M' . $row, $attendance->latitude . ',' . $attendance->longtitude);

            $row++;
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

    /**
     * Undocumented function
     *
     * @param Worksheet $sheet Worksheet Instance
     * @param mixed $image Path to image from storage path
     * @param String $coordinate Excel Coordinate ex: A1 | B2 | C3
     * @return void
     */
    private function createExcelImageCell(
        Worksheet $sheet,
        $image,
        String $coordinate
    ) {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath(Storage::drive('local')->path($image));
        $drawing->setCoordinates($coordinate);
        $drawing->setWidthAndHeight(50, 50);
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);
    }

    private function createExcelHeaderCell(Worksheet $sheet)
    {

        $sheet->mergeCells('B1:M1');
        $sheet->setCellValue('B1', 'Attendance Report - Details');
        $sheet->getStyle('B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
        ]);
        $sheet->mergeCells('A3:A4');
        $sheet->setCellValue('A3', 'Photo');

        $sheet->mergeCells('B3:B4');
        $sheet->setCellValue('B3', 'NIK');

        $sheet->mergeCells('C3:C4');
        $sheet->setCellValue('C3', 'NAMA');

        $sheet->mergeCells('D3:D4');
        $sheet->setCellValue('D3', 'Kode Jadwal');

        $sheet->mergeCells('E3:F3');
        $sheet->setCellValue('E3', 'Jadwal Detail');
        $sheet->setCellValue('E4', 'Dutty On');
        $sheet->setCellValue('F4', 'Dutty Off');

        $sheet->mergeCells('G3:H3');
        $sheet->setCellValue('G3', 'Aktual Detail');
        $sheet->setCellValue('G4', 'Datetime');
        $sheet->setCellValue('H4', 'Tipe Clock');

        $sheet->mergeCells('I3:I4');
        $sheet->setCellValue('I3', 'Tipe Attendance');

        $sheet->mergeCells('J3:J4');
        $sheet->setCellValue('J3', 'Keterangan');

        $sheet->mergeCells('K3:K4');
        $sheet->setCellValue('K3', 'Photo Capture');

        $sheet->mergeCells('L3:L4');
        $sheet->setCellValue('L3', 'Lokasi');

        $sheet->mergeCells('M3:M4');
        $sheet->setCellValue('M3', 'Koordinat');

        $sheet->getStyle('A3:M4')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }
}
