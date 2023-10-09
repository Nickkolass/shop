<?php

namespace App\Http\Requests\API\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductsRequest extends FormRequest
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
            'page' => 'nullable|integer',
            'filter' => 'nullable|array',
            'filter.tags' => 'nullable|array',
            'filter.tags.*' => 'nullable|int',
            'filter.salers' => 'nullable|array',
            'filter.salers.*' => 'nullable|int',
            'filter.optionValues' => 'nullable|array',
            'filter.optionValues.*' => 'nullable|array',
            'filter.optionValues.*.*' => 'nullable|int',
            'filter.propertyValues' => 'nullable|array',
            'filter.propertyValues.*' => 'nullable|array',
            'filter.propertyValues.*.*' => 'nullable|int',
            'filter.prices' => 'nullable|array',
            'filter.prices.min' => 'nullable|int',
            'filter.prices.max' => 'nullable|int',
            'filter.search' => 'nullable|string',
            'paginate' => 'nullable|array',
            'paginate.orderBy' => 'nullable|string',
            'paginate.perPage' => 'nullable|integer',
            'cart' => 'nullable|array',
            'cart.*' => 'nullable|int',
        ];
    }
}
