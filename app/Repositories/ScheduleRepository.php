<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Storage;
use Yajra\DataTables\Facades\DataTables;

class ScheduleRepository extends BaseRepository
{
    public function __construct(Schedule $model)
    {
        parent::__construct($model);
    }

    /**
     * Filtering builder with Request|FormRequest
     *
     * @param   Builder  $schedule  Eloquent Builder instance
     * @param   mixed    $request       Request | FormRequest
     *
     * @return  Builder                 Builder
     */
    private function filter(Builder $schedule, $request)
    {

        $request->get('title') && $schedule->where('title', 'like', '%' . $request->get('title') . '%');

        $request->get('description') && $schedule->where('description', 'like', '%' . $request->get('description') . '%');

        $request->get('start_date') && $schedule->where('start_date', '>=', $request->get('start_date'));

        $request->get('end_date') && $schedule->where('end_date', '<=', $request->get('end_date'));
        return $schedule;
    }

    public function findFilter(Request $request)
    {
        $model = Schedule::select('schedules.*');
        $schedule = $this->filter($model, $request);
        return $schedule->get();
    }

    public function paginate(Request $request)
    {
        $userTable = with(new User)->getTable();
        $scheduleTable = with(new Schedule)->getTable();
        $model = Schedule::select("${scheduleTable}.*", "{$userTable}.nama as user_nama");
        $model->join($userTable, "{$userTable}.id", "=", "${scheduleTable}.user_id");
        return DataTables::eloquent($model)
            ->filter(function ($schedule) use ($request) {
                $this->filter($schedule, $request);
            })->toArray();
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Schedule
     */
    public function create(FormRequest $request)
    {
        $schedule = new Schedule();
        $schedule->code = $request->post('code');
        $schedule->user_id = $request->post('user_id');
        $schedule->duty_on = $request->post('duty_on');
        $schedule->duty_off = $request->post('duty_off');
        $schedule->save();

        return $schedule;
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Schedule
     */
    public function update(FormRequest $request)
    {
        $schedule = Schedule::findOrFail($request->post('id'));
        $schedule->code = $request->post('code');
        $schedule->user_id = $request->post('user_id');
        $schedule->duty_on = $request->post('duty_on');
        $schedule->duty_off = $request->post('duty_off');
        $schedule->save();

        return $schedule;
    }
}
