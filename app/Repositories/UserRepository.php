<?php

namespace App\Repositories;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Ramsey\Uuid\Uuid;
use Storage;
use Yajra\DataTables\Facades\DataTables;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function select2(Request $request)
    {
        $user = User::select('id', 'nama', 'nik');
        if ($request->get('search')) {
            $user->where(function ($multiWhere) use ($request) {
                $multiWhere->where('users.nama', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('users.nik', 'like',  $request->get('search') . '%');
            });
        }
        return $user->orderBy('users.nama', 'asc')->paginate(20);
    }

    public function paginate(Request $request)
    {

        return DataTables::eloquent(User::query())
            ->filter(function ($user) use ($request) {
                $request->get('nama') && $user->where('nama', 'like', "%{$request->get('nama')}%");
                $request->get('email') && $user->where('email', 'like', "%{$request->get('email')}%");
                $request->get('nik') && $user->where('nik', 'like', "%{$request->get('nik')}%");
            })
            ->toArray();
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/User
     */
    public function create(FormRequest $request)
    {
        $user = new User();
        $user->nama = $request->post('nama');
        $user->email = $request->post('email');
        $user->password = Hash::make($request->post('password'));
        $user->tgl_lahir = $request->post('tgl_lahir');
        $user->kota_lahir = $request->post('kota_lahir');
        $user->alamat = $request->post('alamat');
        $user->divisi = $request->post('divisi');
        $user->subdivisi = $request->post('subdivisi');
        $user->company = $request->post('company');
        $user->department = $request->post('department');
        $user->jabatan = $request->post('jabatan');
        $user->bagian = $request->post('bagian');
        $user->lokasi = $request->post('lokasi');
        $user->whatsapp = $request->post('whatsapp');
        $user->sex = $request->post('sex');
        $user->alamat = $request->post('alamat');
        $user->blood = $request->post('blood');
        $user->nik = $request->post('nik');
        $token = Uuid::uuid4();
        $user->token = hash('sha256', $token);
        $user->device_id = $request->post('device_id');
        $user->save();

        return $user;
    }

    /**
     * @param FromRequest $request
     * 
     * @return App/Models/User
     */
    public function update(FormRequest $request, $id = null)
    {
        $user = User::findOrFail($request->post('id') ? $request->post('id') : $id);
        if ($request->post('password')) {
            $user->password = Hash::make($request->post('password'));
        }
        $user->nama = $request->post('nama');
        $user->email = $request->post('email');
        $user->tgl_lahir = $request->post('tgl_lahir');
        $user->kota_lahir = $request->post('kota_lahir');
        $user->alamat = $request->post('alamat');
        $user->divisi = $request->post('divisi');
        $user->subdivisi = $request->post('subdivisi');
        $user->company = $request->post('company');
        $user->department = $request->post('department');
        $user->jabatan = $request->post('jabatan');
        $user->bagian = $request->post('bagian');
        $user->lokasi = $request->post('lokasi');
        $user->whatsapp = $request->post('whatsapp');
        $user->sex = $request->post('sex');
        $user->alamat = $request->post('alamat');
        $user->blood = $request->post('blood');
        $user->nik = $request->post('nik');
        $user->device_id = $request->post('device_id');
        $user->save();
        return $user;
    }

    public function changePassword(FormRequest $formRequest, User $user)
    {
        $user->password = Hash::make($formRequest->post('password'));
        $user->save();

        return $user;
    }

    public function updateProfile(FormRequest $formRequest)
    {

        $user = $formRequest->user();
        if ($formRequest->file('avatar')) {
            $avatar = $formRequest->file('avatar')->store('public/avatars');

            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }
            $user->avatar = Storage::url($avatar);
        }

        if ($formRequest->post('nama')) {
            $user->nama = $formRequest->post('nama');
        }
        if ($formRequest->post('alamat')) {
            $user->alamat = $formRequest->post('alamat');
        }
        if ($formRequest->post('whatsapp')) {
            $user->whatsapp = $formRequest->post('whatsapp');
        }
        if ($formRequest->post('email')) {
            $user->email = $formRequest->post('email');
        }
        $user->save();

        return $user;
    }

    public function toggleLockAccount(User $user, $lock = false)
    {
        $user->lock = $lock ? 1 : 0;
        $user->save();
        return $user;
    }
}
