<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = ['nome', 'descricao', 'local', 'data_evento', 'horario', 'max_participantes'];

    public function inscricoes()
    {
        return $this->hasMany(Inscricao::class);
    }

    public function vagasDisponiveis()
    {
        return $this->max_participantes - $this->inscricoes()->count();
    }
}
