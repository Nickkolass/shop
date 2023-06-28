<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'=> 'required|string|email|unique:users',
            'password'=> 'required|string|confirmed',
            'name' => 'required|string',
            'surname' => 'required|string',
            'patronymic' => 'nullable|string',
            'gender' => 'required|integer',
            'age' => 'required|integer',
            'postcode' => 'nullable|integer',
            'address' => 'nullable|string',
            'INN' => 'nullable|integer|unique:users',
            'registredOffice' => 'nullable|string',
     
        ];

    }
}
