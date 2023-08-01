<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
        $password = url()->previousPath() == '/users/create' ? 'nullable' : 'required';
        return [
            'role' => 'required|integer',
            'email'=> 'required|string|email|unique:users',
            'password'=> [$password, 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->uncompromised()],
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

    public function messages()
    {
        return [
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',
            'INN.unique' => 'Пользователь с таким ИНН уже зарегистрирован',
            'password' => 'Новый пароль должен быть не менее 8 символов, содержать 1 цифру, 1 заглавную и 1 строчную буквы, а также должен пройти проверку на компрометацию',
        ];
    }
}
