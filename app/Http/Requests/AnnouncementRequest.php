<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|min:3',
            'description' => 'required|min:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'attachment' => 'mimes:jpeg,bmp,png,jpg,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
        ];
    }
}
