<?php

namespace App\Http\Requests;

use BenSampo\Enum\Rules\Enum;
use App\Enums\SexType;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nama' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->post('id') . ',id',
            'password' => $this->post('id')  ? '' :  'required|min:8',
            'tgl_lahir' => 'required|date',
            'kota_lahir' => 'required',
            'divisi' => 'required',
            'subdivisi' => 'required',
            'company' => 'required',
            'department' => 'required',
            'jabatan' => 'required',
            'bagian' => 'required',
            'lokasi' => 'required',
            'sex' => ['required', new EnumValue(SexType::class, false)],
            'alamat' => 'required',
            'blood' => 'required',
            'nik' => 'unique:users,nik,' .  $this->post('id') . ',id',
        ];
    }
}
