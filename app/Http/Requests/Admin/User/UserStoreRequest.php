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
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        $password_rule = url()->previousPath() == '/users/create' ? ['required', 'nullable'] : ['nullable', 'required'];
        return [
            'role' => [$password_rule[0], 'integer'],
            'email' => 'required|string|email|unique:users',
            'password' => [$password_rule[1], 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->uncompromised()],
            'name' => 'required|string',
            'surname' => 'required|string',
            'patronymic' => 'nullable|string',
            'gender' => 'required|bool',
            'age' => 'required|integer',

            'postcode' => 'nullable|integer',
            'address' => 'nullable|string',

            'INN' => 'nullable|integer|unique:users',
            'registredOffice' => 'nullable|string',
        ];

    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',
            'INN.unique' => 'Пользователь с таким ИНН уже зарегистрирован',
            'password.confirmed' => 'Пароли должны совпадать',
            'password' => 'Пароль должен быть не менее 8 символов, содержать 1 цифру, 1 заглавную и 1 строчную буквы, а также должен пройти проверку на компрометацию',
        ];
    }
}
