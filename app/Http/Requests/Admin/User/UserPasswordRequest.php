<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserPasswordRequest extends FormRequest
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
            'password'=> 'required|string|current_password',
            'new_password'=> ['required', 'string', 'confirmed', 'different:password', Password::min(8)->mixedCase()->numbers()->uncompromised()],
        ];
    }

    public function messages()
    {
        return [
            'password.current_password' => 'Веден неверный пароль',
            'new_password.confirmed' => 'Новый пароль не совпадает с повторно введенным',
            'new_password.different' => 'Старый и новый пароли одинаковы',
        ];
    }
}