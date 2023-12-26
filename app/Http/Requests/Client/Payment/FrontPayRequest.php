<?php

namespace App\Http\Requests\Client\Payment;

use Illuminate\Foundation\Http\FormRequest;

class FrontPayRequest extends FormRequest
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
            'order_id' => 'required|int',
            'price' => 'required|int',
        ];
    }
}
