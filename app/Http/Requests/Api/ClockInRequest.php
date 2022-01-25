<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ClockInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'location_id' => 'required_if:type,0',
            'type' => 'required',
            'image' => 'required_if:type,1|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'latitude' => 'required',
            'longtitude' => 'required'
        ];
    }
}
