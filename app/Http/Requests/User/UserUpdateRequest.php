<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'user_id' => 'required|int:exists:users,id',
            'email'=> 'required|string|email|unique:users,email,' . $this->user_id,
            'name' => 'required|string',
            'surname' => 'required|string',
            'patronymic' => 'nullable|string',
            'age' => 'required|integer',
            'postcode' => 'nullable|integer',
            'address' => 'nullable|string',
            'INN' => 'nullable|integer|unique:users,INN,' . $this->user_id,
            'registredOffice' => 'nullable|string',
        ];
    }
}
