<?php

namespace App\Http\Requests\Admin;

class UserUpdateCrudRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
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
            'email'    => 'required',
            'name'     => 'required',
            'contact_id' => 'nullable|numeric',
            'password' => 'confirmed',
            'phone1'   => 'required',
        ];
    }
}