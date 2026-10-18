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
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Черновик можно сохранить недописанным: хватит заголовка или текста
        if ($this->isDraft()) {
            return [
                'title' => 'nullable|required_without:body|max:255',
                'body' => 'nullable|required_without:title',
                'tags' => 'array|nullable',
                'tags.*' => 'exists:tags,id'
            ];
        }

        return [
            'title' => 'required|max:255',
            'body' => 'required',
            'tags' => 'array|nullable',
            'tags.*' => 'exists:tags,id'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Укажите заголовок.',
            'body.required' => 'Напишите текст поста.',
            'title.required_without' => 'Чтобы сохранить черновик, заполните заголовок или текст.',
            'body.required_without' => 'Чтобы сохранить черновик, заполните заголовок или текст.',
        ];
    }

    /** Нажата кнопка "В черновик" */
    public function isDraft(): bool
    {
        return $this->input('action') === 'draft';
    }
}
