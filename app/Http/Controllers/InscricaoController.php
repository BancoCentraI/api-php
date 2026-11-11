<?php

namespace App\Http\Controllers;

use App\Http\Resources\InscricaoResource;
use App\Models\Evento;
use App\Models\Inscricao;
use Illuminate\Http\Request;

class InscricaoController extends Controller
{
    public function index(Request $request)
    {
        return InscricaoResource::collection($request->user()->inscricoes);
    }

    public function store(Request $request, Evento $evento)
    {
        $user = $request->user();

        if ($user->inscricoes()->where('evento_id', $evento->id)->exists()) {
            return response()->json(['mensagem' => 'Você já está inscrito neste evento.'], 422);
        }

        if ($evento->vagasDisponiveis() <= 0) {
            return response()->json(['mensagem' => 'Evento lotado. Não há vagas disponíveis.'], 422);
        }

        $inscricao = $user->inscricoes()->create([
            'evento_id' => $evento->id,
            'data_inscricao' => now(),
        ]);

        return new InscricaoResource($inscricao);
    }

    public function destroy(Request $request, Inscricao $inscricao)
    {
        if ($inscricao->user_id != $request->user()->id) {
            return response()->json(['mensagem' => 'Você não pode cancelar a inscrição de outro usuário.'], 403);
        }

        $inscricao->delete();

        return response()->json(['mensagem' => 'Inscrição cancelada. A vaga foi liberada.']);
    }
}
