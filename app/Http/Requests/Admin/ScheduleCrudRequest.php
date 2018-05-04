<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 26-Apr-18
 * Time: 13:48
 */

namespace App\Http\Requests\Admin;


class ScheduleCrudRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
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
            'name' => 'required|max:255',
            'schedule_type' => 'required'
        ];
    }

}