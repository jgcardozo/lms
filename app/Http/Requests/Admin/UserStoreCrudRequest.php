<?php

namespace App\Http\Requests\Admin;

class UserStoreCrudRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
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
            'email'    => 'required|unique:'.config('laravel-permission.table_names.users', 'users').',email',
            'name'     => 'required',
            'contact_id' => 'nullable|numeric',
            'password' => 'required|confirmed',
            'phone1'   => 'required',
        ];
    }
}