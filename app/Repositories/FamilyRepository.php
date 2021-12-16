<?php

namespace App\Repositories;

use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FamilyRepository extends BaseRepository
{
    public function __construct(Family $model)
    {
        parent::__construct($model);
    }

    public function findByUserId($id)
    {
        return $this->model->whereUserId($id)->get();
    }

    public function paginate(Request $request)
    {
        $userTable = with(new User)->getTable();
        $familyTable = with(new Family)->getTable();
        $model = Family::select('families.*', 'users.nama as user_nama')
            ->join($userTable, $userTable . '.id', '=', $familyTable . '.user_id');

        return DataTables::eloquent($model)
            ->filter(function ($family) use ($request) {
                $request->get('nama') && $family->where('families.nama', 'like', "%{$request->get('nama')}%");

                $request->get('user_nama') && $family->where('users.nama', 'like', "%{$request->get('user_nama')}%");
            })
            ->toArray();
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Family
     */
    public function create(FormRequest $request)
    {
        $family = new Family();
        $family->nama = $request->post('nama');
        $family->user_id = $request->post('user_id');
        $family->hubungan = $request->post('hubungan');
        $family->sex = $request->post('sex');
        $family->tempat_lahir = $request->post('tempat_lahir');
        $family->save();

        return $family;
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/Family
     */
    public function update(FormRequest $request)
    {
        $family = Family::find($request->post('id'));
        $family->nama = $request->post('nama');
        $family->user_id = $request->post('user_id');
        $family->hubungan = $request->post('hubungan');
        $family->sex = $request->post('sex');
        $family->tempat_lahir = $request->post('tempat_lahir');
        $family->save();
        return $family;
    }
}
