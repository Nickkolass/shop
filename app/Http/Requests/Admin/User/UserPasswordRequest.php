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
    public function authorize(): bool
    {
        return $this->user()->can('password', $this->route()->parameter('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'password' => 'required|string|current_password',
            'new_password' => ['required', 'string', 'confirmed', 'different:password', Password::min(8)->mixedCase()->numbers()->uncompromised()],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'password.current_password' => 'Веден неверный пароль',
            'new_password.confirmed' => 'Новый пароль не совпадает с повторно введенным',
            'new_password.different' => 'Старый и новый пароли одинаковы',
            'new_password' => 'Новый пароль должен быть не менее 8 символов, содержать 1 цифру, 1 заглавную и 1 строчную буквы, а также должен пройти проверку на компрометацию',
        ];
    }
}
