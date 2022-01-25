<?php

namespace App\Repositories;

use App\Enums\AttendanceType;
use App\Enums\ClockType;
use App\Enums\SexType;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Schedule;
use App\Models\User;
use Cache\Adapter\Redis\RedisCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
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
use Image;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;

class AttendanceRepository extends BaseRepository
{
    protected $scheduleRepository;

    protected $isExcelWithImage = true;

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
        $attendance->location_id = $request->post('type') == AttendanceType::LIVE ? $request->post('location_id')  : null;
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

        $message = __('attendance.success');
        if ($schedule) {
            if (
                $clockType == ClockType::IN &&
                $request->post('type') == AttendanceType::LIVE &&
                $now->gt($schedule->duty_on->addMinutes(1))
            ) {
                // $isLate = $now->gt($schedule->duty_on->addMinutes(1)) ? 1 : 0;
                $message = __('attendance.late');
            }

            if (
                $clockType == ClockType::OUT &&
                $request->post('type') == AttendanceType::LIVE &&
                $now->lt($schedule->duty_off)
            ) {
                // $isLate = $now->lt($schedule->duty_on) ? 2 : 0;
                $message = __('attendance.early');
            }
        }

        return [
            'attendance' => $attendance,
            'message' => $message
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

    public function findAllTest(array $array)
    {

        $userTable = with(new User)->getTable();
        $attendancesTable = with(new Attendance)->getTable();
        $locationTable = with(new Location)->getTable();
        $scheduleTable = with(new Schedule)->getTable();
        $attendances = Attendance::select('attendances.*', $userTable . '.nama as user_nama', $userTable . '.nik as nik', $userTable . '.avatar as user_image', $userTable . '.sex', "{$scheduleTable}.duty_on", "{$scheduleTable}.duty_off", "{$scheduleTable}.code as schedule_code")
            ->leftJoin($scheduleTable, $scheduleTable . '.id', '=', $attendancesTable . '.schedule_id')
            ->join($userTable, $userTable . '.id', '=', $attendancesTable . '.user_id');

        (isset($array['nameOrNIK']) && $array['nameOrNIK']) && $attendances->where(function ($multiWhere) use ($array) {
            $multiWhere->where('users.nama', 'like', "%{$array['nameOrNIK']}%");
            $multiWhere->orWhere('users.nik', $array['nameOrNIK']);
        });

        (isset($array['attendanceType']) && $array['attendanceType'] != '') && $attendances->where('attendances.type', $array['attendanceType']);

        (isset($array['start_date'])) && $attendances->where('attendances.check_clock', '>=', $array['start_date']);

        (isset($array['end_date']))  && $attendances->where('attendances.check_clock', '<=', $array['end_date']);

        return $attendances->get();
    }

    public function findAll(Request $request)
    {
        $attendances = $this->baseAttendance($request);
        $this->filterPaginated($attendances, $request);

        return $attendances->get();
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
                $data['image'] = route('attendance.image', ['path' => $transform->getRawOriginal('image')]);
                $data['user_image'] = $data['user_image'] ? Storage::url($data['user_image']) : null;
                $data['check_clock'] = $transform->check_clock->format('d/m/Y H:i:s');
                $data['duty_on'] = Carbon::parse($transform->duty_on)->format('d/m/Y H:i:s');
                $data['duty_off'] = Carbon::parse($transform->duty_off)->format('d/m/Y H:i:s');
                return $data;
            })
            ->toArray();
    }

    public function makeExcel(Request $request)
    {

        $this->isExcelWithImage = !$request->get('without_image');

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

            $this->createExcelImageCell($sheet, $attendance->user_image ? $attendance->user_image : $defaultImage, 'A' . $row);

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

            $this->createExcelImageCell($sheet, $attendance->getRawOriginal('image'), 'K' . $row);

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
        $writer->setPreCalculateFormulas(false);
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
     * Add Image to cell
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
        if (Storage::exists($image) && $this->isExcelWithImage) {
            Storage::makeDirectory('temp');
            $fileName = storage_path('app/temp/' . pathinfo(Storage::path($image), PATHINFO_FILENAME) . 'jpg');
            $img = Image::cache(function ($oImage) use ($image, $fileName) {
                return $oImage->make(Storage::path($image))->resize(50, 50)->save($fileName, 80);
            });

            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath($fileName);
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
            $toArray = $attendance->toArray();
            $toArray['duty_on'] = Carbon::parse($attendance->duty_on)->format('d/m/Y H:i');
            $toArray['duty_off'] =  Carbon::parse($attendance->duty_off)->format('d/m/Y H:i');
            $toArray['check_clock'] =  Carbon::parse($attendance->check_clock)->format('d/m/Y H:i');
            $toArray['image'] = $attendance->getRawOriginal('image');
            $data[$attendance->user_id][$attendance->duty_on][] = $toArray;
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
                    $sheet->getRowDimension($row)->setRowHeight(55);
                    /** We resize the image to 50px in createExcelImageCell(), to avoid memory leaks */
                    $this->createExcelImageCell($sheet, $perDateAttendace[$i]['user_image'], 'A' . $row);
                    $sheet->setCellValue('B' . $row, $perDateAttendace[$i]['nik']);
                    $sheet->setCellValue('C' . $row, $perDateAttendace[$i]['user_nama']);
                    $sheet->setCellValue('D' . $row, $perDateAttendace[$i]['schedule_code']);
                    $sheet->setCellValue('E' . $row, $perDateAttendace[$i]['duty_off']);
                    $sheet->setCellValue('F' . $row, $perDateAttendace[$i]['duty_off']);

                    //Start Check Clock 
                    $perDateAttendace[$i]['clock_type'] == ClockType::IN && $sheet->setCellValue('G' . $row, $perDateAttendace[$i]['check_clock']);

                    if (isset($perDateAttendace[$i + 1])) {
                        $perDateAttendace[$i + 1]['clock_type'] == ClockType::OUT && $sheet->setCellValue('H' . $row, $perDateAttendace[$i + 1]['check_clock']);
                    }
                    //End Check Clock 

                    $sheet->setCellValue('I' . $row, AttendanceType::getKey($perDateAttendace[$i]['type']));

                    #Start Description / Keterangan
                    $perDateAttendace[$i]['clock_type'] == ClockType::IN && $sheet->setCellValue('J' . $row, $perDateAttendace[$i]['description']);
                    if (isset($perDateAttendace[$i + 1])) {
                        $perDateAttendace[$i + 1]['clock_type'] == ClockType::OUT && $sheet->setCellValue('K' . $row, $perDateAttendace[$i + 1]['description']);
                    }
                    #END Description / Keterangan


                    #Start Image Capture
                    $perDateAttendace[$i]['clock_type'] == ClockType::IN && $this->createExcelImageCell($sheet, $perDateAttendace[$i]['image'], 'L' . $row);
                    if (isset($perDateAttendace[$i + 1])) {
                        $perDateAttendace[$i + 1]['clock_type'] == ClockType::OUT &&   $this->createExcelImageCell($sheet, $perDateAttendace[$i + 1]['image'], 'M' . $row);
                    }
                    #END Image Capture


                    $perDateAttendace[$i]['clock_type'] == ClockType::IN && $sheet->setCellValue('N' . $row, $perDateAttendace[$i]['location_name']);
                    if (isset($perDateAttendace[$i + 1])) {
                        $perDateAttendace[$i + 1]['clock_type'] == ClockType::OUT && $sheet->setCellValue('O' . $row, $perDateAttendace[$i]['location_name']);
                    }

                    $sheet->setCellValue('P' . $row, $perDateAttendace[$i]['latitude'] . ',' . $perDateAttendace[$i]['longtitude']);

                    (isset($perDateAttendace[$i + 1]) && $perDateAttendace[$i + 1]['clock_type'] == ClockType::OUT) && $i++;
                    $row++;
                }
                // if ($perDateAttendace[$i]['clock_type'] == 0) {
                //     $i++;
                // }

            }
        }
        $sheet->getStyle('A1:P' . $row)->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:P' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:P' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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
        $sheet->getColumnDimension('B')->setWidth(10);

        $sheet->mergeCells('C3:C4');
        $sheet->setCellValue('C3', 'NAMA');
        $sheet->getColumnDimension('C')->setWidth(14);

        $sheet->mergeCells('D3:D4');
        $sheet->setCellValue('D3', 'Kode Jadwal');
        $sheet->getColumnDimension('D')->setWidth(14);

        $sheet->mergeCells('E3:F3');
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->setCellValue('E3', 'Jadwal Detail');
        $sheet->setCellValue('E4', 'Dutty On');
        $sheet->setCellValue('F4', 'Dutty Off');

        $sheet->mergeCells('G3:H3');
        $sheet->getColumnDimension('G')->setWidth(16);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->setCellValue('G3', 'Aktual Detail');
        $sheet->setCellValue('G4', 'In');
        $sheet->setCellValue('H4', 'Out');

        $sheet->mergeCells('I3:I4');
        $sheet->getColumnDimension('I')->setWidth(16);
        $sheet->setCellValue('I3', 'Tipe Attendance');

        $sheet->mergeCells('J3:K3');
        $sheet->getColumnDimension('J')->setWidth(16);
        $sheet->getColumnDimension('K')->setWidth(16);
        $sheet->setCellValue('J3', 'Keterangan');
        $sheet->setCellValue('J4', 'In');
        $sheet->setCellValue('K4', 'Out');

        $sheet->mergeCells('L3:M3');
        $sheet->getColumnDimension('L')->setWidth(16);
        $sheet->getColumnDimension('M')->setWidth(16);
        $sheet->setCellValue('L3', 'Photo Capture');
        $sheet->setCellValue('L4', 'In');
        $sheet->setCellValue('M4', 'Out');

        $sheet->mergeCells('N3:O3');
        $sheet->getColumnDimension('N')->setWidth(10);
        $sheet->getColumnDimension('O')->setWidth(10);
        $sheet->setCellValue('N3', 'Lokasi');
        $sheet->setCellValue('N4', 'In');
        $sheet->setCellValue('O4', 'Out');

        $sheet->mergeCells('P3:P4');
        $sheet->getColumnDimension('P')->setWidth(16);
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
