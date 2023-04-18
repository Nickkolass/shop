<?php

namespace App\Http\Requests\Option;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'optionValues' => 'required|array',
        ];
    }
}
