<?php

namespace App\Repositories;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LocationRepository extends BaseRepository
{
    public function __construct(Location $model)
    {
        parent::__construct($model);
    }

    public function paginate(Request $request)
    {

        return DataTables::eloquent(Location::query())
            ->filter(function ($location) use ($request) {
                $request->get('nama') && $location->where('nama', 'like', "%{$request->get('nama')}%");
            })
            ->toArray();
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Location
     */
    public function create(FormRequest $request)
    {
        $location = new Location();
        $location->nama = $request->post('nama');
        $location->latitude = $request->post('latitude');
        $location->longtitude = $request->post('longtitude');
        $location->radius = $request->post('radius');
        $location->save();

        return $location;
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Location
     */
    public function update(FormRequest $request)
    {
        $location = Location::find($request->post('id'));
        $location->nama = $request->post('nama');
        $location->latitude = $request->post('latitude');
        $location->longtitude = $request->post('longtitude');
        $location->radius = $request->post('radius');
        $location->save();
        return $location;
    }
}
