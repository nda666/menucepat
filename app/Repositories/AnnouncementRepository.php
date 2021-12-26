<?php

namespace App\Repositories;

use App\Models\Announcement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Storage;
use Yajra\DataTables\Facades\DataTables;

class AnnouncementRepository extends BaseRepository
{
    public function __construct(Announcement $model)
    {
        parent::__construct($model);
    }

    /**
     * Filtering builder with Request|FormRequest
     *
     * @param   Builder  $announcement  Eloquent Builder instance
     * @param   mixed    $request       Request | FormRequest
     *
     * @return  Builder                 Builder
     */
    private function filter(Builder $announcement, $request)
    {

        $request->get('title') && $announcement->where('title', 'like', '%' . $request->get('title') . '%');

        $request->get('description') && $announcement->where('description', 'like', '%' . $request->get('description') . '%');

        $request->get('start_date') && $announcement->where('start_date', '>=', $request->get('start_date'));

        $request->get('end_date') && $announcement->where('end_date', '<=', $request->get('end_date'));
        return $announcement;
    }

    public function findFilter(Request $request)
    {
        $model = Announcement::select('announcements.*');
        $announcement = $this->filter($model, $request);
        return $announcement->get();
    }

    public function paginate(Request $request)
    {
        // $userTable = with(new User)->getTable();
        // $announcementTable = with(new Announcement)->getTable();
        $model = Announcement::select('announcements.*');
        return DataTables::eloquent($model)
            ->filter(function ($announcement) use ($request) {
                $this->filter($announcement, $request);
            })->toArray();
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Announcement
     */
    public function create(FormRequest $request)
    {
        $announcement = new Announcement();
        $announcement->title = $request->post('title');
        $announcement->description = $request->post('description');
        $announcement->start_date = $request->post('start_date');
        $announcement->end_date = $request->post('end_date');

        if ($request->file('attachment')) {
            $attachment = $request->file('attachment')->store('public/attachments');

            if ($announcement->attachment && Storage::exists($announcement->attachment)) {
                Storage::delete($announcement->attachment);
            }

            $announcement->attachment = Storage::url($attachment);
        }

        $announcement->save();

        return $announcement;
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Announcement
     */
    public function update(FormRequest $request)
    {
        $announcement = Announcement::findOrFail($request->post('id'));
        $announcement->title = $request->post('title');
        $announcement->description = $request->post('description');
        $announcement->start_date = $request->post('start_date');
        $announcement->end_date = $request->post('end_date');

        if ($request->file('attachment')) {
            $attachment = $request->file('attachment')->store('public/attachments');

            if ($announcement->attachment && Storage::exists($announcement->attachment)) {
                Storage::delete($announcement->attachment);
            }

            $announcement->attachment = Storage::url($attachment);
        }

        $announcement->save();

        return $announcement;
    }
}
