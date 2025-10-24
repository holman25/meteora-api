<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content'                => ['required', 'string', 'min:1', 'max:4000'],
            'date'                   => ['nullable', 'string', 'max:100'],
            'location'               => ['nullable', 'array'],
            'location.lat'           => ['nullable', 'numeric', 'between:-90,90'],
            'location.lon'           => ['nullable', 'numeric', 'between:-180,180'],
            'model'                  => ['nullable', 'string', 'max:100'],
            'metadata'               => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required'  => 'El contenido del mensaje es obligatorio.',
            'content.min'       => 'El mensaje es demasiado corto.',
            'location.lat.numeric' => 'La latitud debe ser numérica.',
            'location.lon.numeric' => 'La longitud debe ser numérica.',
        ];
    }
}


