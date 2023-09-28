<?php

namespace App\Http\Requests\API\RatingAndComment;

use Illuminate\Foundation\Http\FormRequest;

class StoreFrontRequest extends FormRequest
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
            'rating' => 'required|integer',
            'message' => 'nullable|string',
            'comment_images' => 'nullable|array',
            'comment_images.*' => 'nullable|image',
            'product_id' => 'required|integer',
        ];
    }
}
