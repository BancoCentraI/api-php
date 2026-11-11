<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InscricaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data_inscricao' => $this->data_inscricao->format('d/m/Y H:i'),
            'evento' => new EventoResource($this->evento),
        ];
    }
}
