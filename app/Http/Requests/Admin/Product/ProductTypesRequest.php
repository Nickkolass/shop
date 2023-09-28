<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductTypesRequest extends FormRequest
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

        foreach ($this->input('types') as $k => $type) $this->merge(["types.$k.relations.optionValues" => array_filter($type['relations']['optionValues'])]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'types' => 'array',
            'types.*.price' => 'required|int',
            'types.*.count' => 'required|int',
            'types.*.is_published' => 'nullable|bool',
            'types.*.preview_image' => 'required|image ',
            'types.*.relations' => 'required|array',
            'types.*.relations.productImages' => 'required|array',
            'types.*.relations.productImages.*' => 'required|image ',
            'types.*.relations.optionValues' => 'required|array',
            'types.*.relations.optionValues.*' => 'required|int',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'types.*.preview_image' => 'Выбор заставки обязателен',
            'types.*.relations.productImages' => 'Выбор изображений обязателен',
            'types.*.relations.productImages.*.image' => 'Выбранные файлы должны быть изображениями',
            'types.*.relations.optionValues.required' => 'Все классификаторы должны быть выбраны',
            'types.*.relations.optionValues.*.required' => 'Все классификаторы должны быть выбраны',
        ];
    }
}
