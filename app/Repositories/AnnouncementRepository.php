<?php

namespace App\Repositories;

use App\Models\Announcement;
use App\Models\User;
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

    public function paginate(Request $request)
    {
        // $userTable = with(new User)->getTable();
        // $announcementTable = with(new Announcement)->getTable();
        $model = Announcement::select('announcements.*');

        return DataTables::eloquent($model)
            ->filter(function ($announcement) use ($request) {
                $request->get('title') && $announcement->where(function ($where) use ($request) {
                    $where->where('title', 'like', '%' . $request->get('title') . '%');
                    $where->where('description', 'like', '%' . $request->get('title') . '%');
                });
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

        $attachment = $request->file('attachment')->store('public/attachments');
        $announcement->attachment = Storage::url($attachment);

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
        $announcement = new Announcement();
        $announcement->title = $request->post('title');
        $announcement->description = $request->post('description');
        $announcement->start_date = $request->post('start_date');
        $announcement->end_date = $request->post('end_date');

        $attachment = $request->file('attachment')->store('public/attachments');

        if ($announcement->attachment && Storage::exists($announcement->attachment)) {
            Storage::delete($announcement->attachment);
        }

        $announcement->attachment = Storage::url($attachment);

        $file = $request->file('avatar')->store('public/attachment');
        $announcement->attachment = Storage::url($file);

        $announcement->save();

        return $announcement;
    }
}
