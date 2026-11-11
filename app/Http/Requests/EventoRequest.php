<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'local' => 'required|string|max:255',
            'data_evento' => 'required|date',
            'horario' => 'required|date_format:H:i',
            'max_participantes' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'date' => 'Informe uma data válida (AAAA-MM-DD).',
            'date_format' => 'Informe o horário no formato HH:MM.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'min' => 'O campo :attribute deve ser no mínimo :min.',
        ];
    }
}
