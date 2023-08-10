<?php

namespace App\Http\Requests\Admin\Product;

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
        session(['validator_failed' => true]);
        $types = $this->types;
        foreach ($types as &$type) {
            if (!empty($optionValues = array_filter($type['optionValues']))) $type['optionValues'] = $optionValues;
            else unset($type['optionValues']);
        }
        $this->merge(['types' => $types]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        session()->forget('validator_failed');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'types' => 'array',
            'types.*.price' => 'required|int',
            'types.*.count' => 'required|int',
            'types.*.is_published' => 'nullable|bool',
            'types.*.preview_image' => 'required|file',
            'types.*.productImages' => 'required|array',
            'types.*.optionValues' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'types.*.optionValues.required' => 'Выбор хотя бы одного классификатора обязателен',
            'types.*.preview_image' => 'Выбор заставки обязателен',
            'types.*.productImages' => 'Выбор изображений обязателен',
        ];
    }
}
