<?php

namespace App\Http\Requests\API\Order;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * Handle a passed validation attempt.
     */
    protected function prepareForValidation(): void
    {
        $user = User::select('surname', 'name', 'patronymic', 'address')->find($this->user_id);

        $this->merge([
            'delivery' => $this->delivery . '. Получатель: ' . $user->surname . ' ' . $user->name . ' ' . $user->patronymic . '. Адрес: ' . $user->address,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer',
            'delivery' => 'required|string',
            'total_price' => 'required|integer',
            'payment_status' => 'required|boolean',
            'cart' => 'required|array',
            'payment' => 'required|string',

        ];
    }
}
