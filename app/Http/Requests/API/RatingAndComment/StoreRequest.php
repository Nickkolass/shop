<?php

namespace App\Http\Requests\API\RatingAndComment;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
        $this->merge(['user_id' => auth('api')->id()]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id'=> ['required', 'integer', Rule::unique('rating_and_comments')
                ->where(fn ($q) => $q->where(['product_id' => $this->product_id, 'user_id' => $this->user_id]))],
            'product_id' => 'required|integer',
            'rating' => 'required|integer',
            'message' => 'nullable|string',
            'comment_images' => 'nullable|array',
            'comment_images.*.path' => 'nullable|string',
            'comment_images.*.originalName' => 'nullable|string',
            'comment_images.*.mimeType' => 'nullable|string',
        ];
    }
}
