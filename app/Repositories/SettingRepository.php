<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Storage;
use Yajra\DataTables\Facades\DataTables;

class SettingRepository extends BaseRepository
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    public function paginate(Request $request)
    {
        // $userTable = with(new User)->getTable();
        // $settingTable = with(new Setting)->getTable();
        $model = Setting::select('settings.*');

        return DataTables::eloquent($model)
            ->filter(function ($setting) use ($request) {
                $request->get('key') && $setting->where('key', 'like', '%' . $request->get('key' . '%'));
                $request->get('value') && $setting->where('value', 'like', '%' . $request->get('value') . '%');
            })->toArray();
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Setting
     */
    public function create(FormRequest $request)
    {
        $setting = new Setting();
        $setting->key = $request->post('key');
        $setting->value = $request->post('value');
        $setting->save();

        return $setting;
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Setting
     */
    public function update(FormRequest $request)
    {
        $setting = Setting::findOrFail($request->post('id'));
        $setting->key = $request->post('key');
        $setting->value = $request->post('value');
        $setting->save();

        return $setting;
    }
}
