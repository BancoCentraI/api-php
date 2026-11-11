<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'local' => $this->local,
            'data_evento' => $this->data_evento,
            'horario' => $this->horario,
            'max_participantes' => $this->max_participantes,
            'vagas_disponiveis' => $this->vagasDisponiveis(),
        ];
    }
}
