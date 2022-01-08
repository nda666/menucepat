<?php

namespace App\Repositories;

use App\Models\UserSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Storage;
use Yajra\DataTables\Facades\DataTables;

class UserScheduleRepository extends BaseRepository
{
    public function __construct(UserSchedule $model)
    {
        parent::__construct($model);
    }

    /**
     * Filtering builder with Request|FormRequest
     *
     * @param   Builder  $userSchedule  Eloquent Builder instance
     * @param   mixed    $request       Request | FormRequest
     *
     * @return  Builder                 Builder
     */
    private function filter(Builder $userSchedule, $request)
    {

        $request->get('title') && $userSchedule->where('title', 'like', '%' . $request->get('title') . '%');

        $request->get('description') && $userSchedule->where('description', 'like', '%' . $request->get('description') . '%');

        $request->get('start_date') && $userSchedule->where('start_date', '>=', $request->get('start_date'));

        $request->get('end_date') && $userSchedule->where('end_date', '<=', $request->get('end_date'));
        return $userSchedule;
    }

    public function findFilter(Request $request)
    {
        $model = UserSchedule::select('user_schedules.*');
        $userSchedule = $this->filter($model, $request);
        return $userSchedule->get();
    }

    public function paginate(Request $request)
    {
        // $userTable = with(new User)->getTable();
        // $userScheduleTable = with(new UserSchedule)->getTable();
        $model = UserSchedule::select('user_schedules.*');
        return DataTables::eloquent($model)
            ->filter(function ($userSchedule) use ($request) {
                $this->filter($userSchedule, $request);
            })->toArray();
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/UserSchedule
     */
    public function create(FormRequest $request)
    {
        $userSchedule = new UserSchedule();
        $userSchedule->title = $request->post('title');
        $userSchedule->description = $request->post('description');
        $userSchedule->start_date = $request->post('start_date');
        $userSchedule->end_date = $request->post('end_date');

        if ($request->file('attachment')) {
            $attachment = $request->file('attachment')->store('public/attachments');

            if ($userSchedule->attachment && Storage::exists($userSchedule->attachment)) {
                Storage::delete($userSchedule->attachment);
            }

            $userSchedule->attachment = Storage::url($attachment);
        }

        $userSchedule->save();

        return $userSchedule;
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/UserSchedule
     */
    public function update(FormRequest $request)
    {
        $userSchedule = UserSchedule::findOrFail($request->post('id'));
        $userSchedule->title = $request->post('title');
        $userSchedule->description = $request->post('description');
        $userSchedule->start_date = $request->post('start_date');
        $userSchedule->end_date = $request->post('end_date');

        if ($request->file('attachment')) {
            $attachment = $request->file('attachment')->store('public/attachments');

            if ($userSchedule->attachment && Storage::exists($userSchedule->attachment)) {
                Storage::delete($userSchedule->attachment);
            }

            $userSchedule->attachment = Storage::url($attachment);
        }

        $userSchedule->save();

        return $userSchedule;
    }
}
