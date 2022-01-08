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
        // $userTable = with(new User)->getTable();
        // $scheduleTable = with(new Schedule)->getTable();
        $model = Schedule::select('schedules.*');
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
        $schedule->title = $request->post('title');
        $schedule->description = $request->post('description');
        $schedule->start_date = $request->post('start_date');
        $schedule->end_date = $request->post('end_date');

        if ($request->file('attachment')) {
            $attachment = $request->file('attachment')->store('public/attachments');

            if ($schedule->attachment && Storage::exists($schedule->attachment)) {
                Storage::delete($schedule->attachment);
            }

            $schedule->attachment = Storage::url($attachment);
        }

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
        $schedule->title = $request->post('title');
        $schedule->description = $request->post('description');
        $schedule->start_date = $request->post('start_date');
        $schedule->end_date = $request->post('end_date');

        if ($request->file('attachment')) {
            $attachment = $request->file('attachment')->store('public/attachments');

            if ($schedule->attachment && Storage::exists($schedule->attachment)) {
                Storage::delete($schedule->attachment);
            }

            $schedule->attachment = Storage::url($attachment);
        }

        $schedule->save();

        return $schedule;
    }
}
