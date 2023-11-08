<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        return [
            'email' => 'required|string|email|unique:users,email,' . $this->input('id'),
            'name' => 'required|string',
            'surname' => 'required|string',
            'patronymic' => 'nullable|string',
            'age' => 'required|integer',

            'postcode' => 'nullable|integer',
            'address' => 'nullable|string',

            'INN' => 'nullable|integer|unique:users,INN,' . $this->input('id'),

            'registredOffice' => 'nullable|string',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',
            'INN.unique' => 'Пользователь с таким ИНН уже зарегистрирован',
            'card.unique' => 'Указанная карта не может быть добавлена',
        ];
    }
}
