<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        $this->merge([
            'propertyValues' => array_filter($this->propertyValues),
            'optionValues' => collect($this->optionValues)->flatten()->all(),
            'tags' => session()->pull('edit.tags'),
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
            'tags' => 'required|array',
            'optionValues' => 'required|array',
            'propertyValues' => 'required|array',
        ];
    }
}
