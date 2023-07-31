<?php

namespace App\Http\Requests\Admin\Property;

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
        $pVs = [];
        foreach ($this->propertyValues as $pV) {
            if (isset($pV['value']) & (array_search($pV, $pVs) === false)) {
                $pVs[] = $pV;
            }
        }
        $this->merge(['propertyValues' => $pVs]);
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
