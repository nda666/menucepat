<?php

namespace App\Repositories;

use App\Enums\AttendanceType;
use App\Enums\ClockType;
use App\Enums\SexType;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
    protected $scheduleRepository;

    public function __construct(Attendance $model, ScheduleRepository $scheduleRepository)
    {
        parent::__construct($model);
        $this->scheduleRepository = $scheduleRepository;
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

    /**
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param int $clocktype ClockType Instance
     * 
     * @return App/Models/Attendance
     */
    public function checkClock(FormRequest $request, int $clockType)
    {
        $schedule = $this->scheduleRepository->getCurrentSchedule(auth()->user()->id);
        $attendance = new Attendance();
        if ($schedule) {
            $attendance->schedule_id = $schedule->id;
        }
        $now = Carbon::now();
        $attendance->check_clock = $now;
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

        $isLate = 0;
        if ($clockType == ClockType::IN && $request->post('type') == AttendanceType::LIVE) {
            $isLate = $now->gt($schedule->duty_on) ? 1 : 0;
        }

        if ($clockType == ClockType::OUT && $request->post('type') == AttendanceType::LIVE) {
            $isLate = $now->gt($schedule->duty_on) ? 2 : 0;
        }

        return [
            'attendance' => $attendance,
            'message' => $isLate ? 'Absensi berhasil, anda terlambat' : 'Absensi berhasil, anda tidak terlambat'
        ];
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
        $scheduleTable = with(new Schedule)->getTable();
        $model = Attendance::select('attendances.*', $userTable . '.nama as user_nama', $userTable . '.nik as nik', $userTable . '.avatar as user_image', $userTable . '.sex', "{$scheduleTable}.duty_on", "{$scheduleTable}.duty_off", "{$scheduleTable}.code as schedule_code")
            ->leftJoin($scheduleTable, $scheduleTable . '.id', '=', $attendancesTable . '.schedule_id')
            ->join($userTable, $userTable . '.id', '=', $attendancesTable . '.user_id');

        return $model;
    }

    /**
     * Filter and paginated attendances
     *
     * @param Builder $attendances
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
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
            ->orderBy('schedules.duty_on', 'asc')
            ->orderBy('attendances.check_clock', 'asc')
            ->get();
        $spreadsheet = new Spreadsheet();
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Attendance Report - Details');
        $spreadsheet->addSheet($worksheet, 0);
        $sheet = $spreadsheet->setActiveSheetIndex(0);


        $this->createExcelHeaderCell($sheet);
        $row = 5;
        foreach ($attendances as $attendance) {
            $defaultImage = 'public/images/avatar-m.png';
            if (SexType::getKey($attendance->sex) == 'FEMALE') {
                $defaultImage = 'public/images/avatar-f.png';
            }
            // $this->createExcelImageCell($sheet, $attendance->user_image ? $attendance->user_image : $defaultImage, 'A' . $row);
            $sheet->getRowDimension($row)->setRowHeight(50);
            $sheet->setCellValue('B' . $row, $attendance->nik);
            $sheet->setCellValue('C' . $row, $attendance->user_nama);
            $sheet->setCellValue('D' . $row, $attendance->schedule_code);
            $sheet->setCellValue('E' . $row, $attendance->duty_on);
            $sheet->setCellValue('F' . $row, $attendance->duty_off);
            $sheet->setCellValue('G' . $row, $attendance->check_clock);
            $sheet->setCellValue('H' . $row, $attendance->clock_type->key);
            $sheet->setCellValue('I' . $row, $attendance->type->key);
            $sheet->setCellValue('J' . $row, $attendance->description);
            // $this->createExcelImageCell($sheet, $attendance->getRawOriginal('image'), 'K' . $row);
            $sheet->setCellValue('L' . $row, $attendance->location_name);
            $sheet->setCellValue('M' . $row, $attendance->latitude . ',' . $attendance->longtitude);
            $row++;
        }

        foreach (range('A', 'M') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $spreadsheet = $this->makeSecondWorksheet($spreadsheet, $attendances);

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
        if (Storage::exists($image)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath(Storage::path($image));
            $drawing->setCoordinates($coordinate);
            $drawing->setWidthAndHeight(50, 50);
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }
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

    private function excelSummaryData(Collection $attendances)
    {
        $data = [];
        foreach ($attendances as $attendance) {
            $data[$attendance->user_id][$attendance->duty_on][] = $attendance->toArray();
        }
        foreach ($data as $userId => $groupedAttendance) {
            foreach ($groupedAttendance as $key =>  $perDateAttendace) {
                usort($data[$userId][$key], function ($a, $b) {
                    return $a['type'] - $b['type'];
                });
            }
        }
        return $data;
    }



    private function makeSecondWorksheet(Spreadsheet $spreadsheet, Collection $attendances)
    {

        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Attendance Report - Summary');
        $sheet = $spreadsheet->addSheet($worksheet, 1);
        $this->createSecondExcelHeaderCell($sheet);
        $groupedAttendances = $this->excelSummaryData($attendances);
        $row = 5;
        foreach ($groupedAttendances as $groupedAttendance) {
            foreach ($groupedAttendance as $key =>  $perDateAttendace) {
                $max = count($perDateAttendace);
                for ($i = 0; $i < $max; $i++) {
                    $sheet->setCellValue('A' . $row, $perDateAttendace[$i]['user_id']);
                    $sheet->setCellValue('B' . $row, $perDateAttendace[$i]['nik']);
                    $sheet->setCellValue('C' . $row, $perDateAttendace[$i]['user_nama']);
                    $row++;
                }
                // if ($perDateAttendace[$i]['clock_type'] == 0) {
                //     $i++;
                // }

            }
        }

        return $spreadsheet;
    }

    private function createSecondExcelHeaderCell(Worksheet $sheet)
    {

        $sheet->mergeCells('B1:M1');
        $sheet->setCellValue('B1', 'Attendance Report - Summary');
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

        $sheet->mergeCells('J3:K3');
        $sheet->setCellValue('J3', 'Keterangan');
        $sheet->setCellValue('J4', 'In');
        $sheet->setCellValue('K4', 'Out');

        $sheet->mergeCells('L3:M3');
        $sheet->setCellValue('L3', 'Photo Capture');
        $sheet->setCellValue('L4', 'In');
        $sheet->setCellValue('M4', 'Out');

        $sheet->mergeCells('N3:O3');
        $sheet->setCellValue('N3', 'Lokasi');
        $sheet->setCellValue('N4', 'In');
        $sheet->setCellValue('O4', 'Out');

        $sheet->mergeCells('P3:P4');
        $sheet->setCellValue('P3', 'Koordinat');

        $sheet->getStyle('A3:P4')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }
}
