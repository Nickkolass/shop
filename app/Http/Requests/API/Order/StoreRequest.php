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
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function prepareForValidation(): void
    {
        $user = auth('api')->user();
        /** @var User $user */
        $this->merge([
            'user_id' => $user->id,
            'delivery' => $this->input('delivery') . ". Получатель: $user->surname $user->name $user->patronymic. Адрес: $user->address",
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
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
