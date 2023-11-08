<?php

namespace App\Http\Requests\Client\RatingAndComment;

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
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'rating' => 'required|integer',
            'message' => 'nullable|string',
            'comment_images' => 'nullable|array',
            'comment_images.*' => 'nullable|image',
            'product_id' => 'required|integer',
        ];
    }
}
