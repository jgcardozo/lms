<?php

namespace App\Http\Requests\Admin;

class SessionCrudRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
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
            'description' => 'required|',
            'video_url' => 'required|max:255',
            'video_duration' => 'required|numeric',
            // 'lesson_id' => 'numeric',
            // 'starter_course_id' => 'numeric'
        ];
    }
}
