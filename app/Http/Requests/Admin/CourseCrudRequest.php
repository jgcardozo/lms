<?php

namespace App\Http\Requests\Admin;

class CourseCrudRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'short_description' => 'required|max:255',
            'description' => 'required',
            'video_url' => 'required|max:255',
            'module_group_title' => 'required|max:255',
            'featured_image' => 'mimes:jpeg,bmp,png'
        ];
    }
}