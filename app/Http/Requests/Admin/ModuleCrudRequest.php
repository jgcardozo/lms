<?php

namespace App\Http\Requests\Admin;

class ModuleCrudRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
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
            'description' => 'required',
            'video_url' => 'required|max:255',
            'course_id' => 'numeric',
            'lesson_group_title' => 'required'
        ];
    }
}