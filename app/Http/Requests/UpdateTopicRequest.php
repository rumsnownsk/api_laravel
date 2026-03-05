<?php

namespace App\Http\Requests;

use App\Models\Topic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTopicRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // ID топика уже загружен в контроллере через Route Model Binding
//        $topicId = $this->route('topic'); // Получаем модель через Route Model Binding

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
//                Rule::unique('topics', 'name')->ignore($topicId)
            ],
            'slug' => ['required'],
            'description' => ['nullable', 'string'] // Добавьте это поле
        ];
    }
}
