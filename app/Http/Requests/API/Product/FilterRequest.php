<?php

namespace App\Http\Requests\API\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class FilterRequest extends FormRequest
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
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if (empty($this['perPage'])) {
            $this->merge([
                'is_published' => 1,
                'perPage' => 8,
                'orderBy' => 'latest',
            ]);
        } else {
            if (!empty($prices = $this['prices'])) {
                if ($prices = array_filter($this['prices'])) {
                    !empty($prices['minPrice']) ? '' : $prices['minPrice'] = Product::where('category_id', $this['category']['id'])->min('price');
                    !empty($prices['maxPrice']) ? '' : $prices['maxPrice'] = Product::where('category_id', $this['category']['id'])->max('price');
                    $prices['minPrice'] > $prices['maxPrice'] ? $prices['minPrice'] = $prices['maxPrice'] : '';
                    $this->merge([
                        'prices' => [
                            'minPrice' => $prices['minPrice'],
                            'maxPrice' => $prices['maxPrice'],
                        ]]);
                }
            }
        }
        $this->merge([
            'page' => isset($this['page']) ? $this['page'] : 1,
            'category' => $this['category']['id'],
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
            'tags' => 'array',
            'colors' => 'array',
            'salers' => 'array',
            'prices' => [
                'minPrice' => '',
                'maxPrice' => '',
            ],
            'orderBy' => 'string',
            'is_published' => 'bool',
            'perPage' => 'integer',
            'page' => 'integer',
            'category' => 'integer',
        ];
    }
}
