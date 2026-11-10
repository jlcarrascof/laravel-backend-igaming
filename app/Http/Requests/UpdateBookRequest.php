<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'author' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'published_year' => ['sometimes', 'required', 'integer', 'min:1000', "max:{$currentYear}"],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título del libro es obligatorio.',
            'title.min' => 'El título debe tener al menos 3 caracteres.',
            'title.max' => 'El título no puede superar los 255 caracteres.',
            'author.required' => 'El autor es obligatorio.',
            'author.max' => 'El autor no puede superar los 255 caracteres.',
            'published_year.required' => 'El año de publicación es obligatorio.',
            'published_year.integer' => 'El año de publicación debe ser un número entero.',
            'published_year.min' => 'El año de publicación debe ser un año válido.',
            'published_year.max' => 'El año de publicación no puede ser mayor al año actual.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe en el sistema.',
        ];
    }
}
