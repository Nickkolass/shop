<?php

namespace App\Http\Requests\API\Product;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'filter' => 'array',
            'filter.tags' => 'array',
            'filter.salers' => 'array',
            'filter.optionValues' => 'array',
            'filter.propertyValues' => 'array',
            'filter.prices' => 'array',
            'filter.search' => 'string',
            'paginate' => 'array',
            'paginate.orderBy' => 'string',
            'paginate.perPage' => 'integer',
            'paginate.page' => 'integer',
            'cart' => 'array',
        ];
    }
}
