<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content'        => ['required', 'string', 'max:5000'],
            'location'       => ['sometimes', 'array'],
            'location.lat'   => ['sometimes', 'numeric'],
            'location.lon'   => ['sometimes', 'numeric'],
            'date'           => ['sometimes', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'content' => 'contenido',
            'location.lat' => 'latitud',
            'location.lon' => 'longitud',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'El contenido es obligatorio.',
            'content.max'      => 'El contenido no puede superar 5000 caracteres.',
            'location.array'   => 'La ubicación debe ser un objeto JSON.',
            'location.lat.numeric' => 'La latitud debe ser numérica.',
            'location.lon.numeric' => 'La longitud debe ser numérica.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('date') && $this->input('date') === '') {
            $this->merge(['date' => null]);
        }
    }
}

