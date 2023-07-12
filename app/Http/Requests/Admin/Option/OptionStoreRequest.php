<?php

namespace App\Http\Requests\Admin\Option;

use Illuminate\Foundation\Http\FormRequest;

class OptionStoreRequest extends FormRequest
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
        $oVs = [];
        foreach ($this->optionValues as $oV) {
            if (isset($oV['value']) & (array_search($oV, $oVs) === false)) {
                $oVs[] = $oV;
            }
        }
        $this->merge(['optionValues' => $oVs]);
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
