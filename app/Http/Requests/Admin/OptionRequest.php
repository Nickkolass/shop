<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OptionRequest extends FormRequest
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
        $optionValues = [];
        foreach ($this->optionValues as $optionValue) {
            if (isset($optionValue['value']) & (array_search($optionValue, $optionValues) === false)) {
                $optionValues[] = $optionValue;
            }
        }
        $this->merge(['optionValues' => $optionValues]);
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
            'optionValues' => 'required|array',
            'optionValues.*.value' => 'required|string',
        ];
    }
}
