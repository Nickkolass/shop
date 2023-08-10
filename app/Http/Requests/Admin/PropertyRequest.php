<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
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
        $propertyValues = [];
        foreach ($this->propertyValues as $propertyValue) {
            if (isset($propertyValue['value']) & (array_search($propertyValue, $propertyValues) === false)) {
                $propertyValues[] = $propertyValue;
            }
        }
        $this->merge(['propertyValues' => $propertyValues]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'categories' => 'required|array',
            'propertyValues' => 'required|array',
            'propertyValues.*.value' => 'required|string',
        ];
    }
}
