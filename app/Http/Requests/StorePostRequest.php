<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255|string',
            'body' => 'required|string',
            'topic_id' => 'required|integer|exists:topics,id',
            'description' => 'string|nullable|max:65535',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id'
        ];
    }
}
