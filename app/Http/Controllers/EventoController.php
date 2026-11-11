<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventoRequest;
use App\Http\Resources\EventoResource;
use App\Models\Evento;

class EventoController extends Controller
{
    public function index()
    {
        return EventoResource::collection(Evento::all());
    }

    public function store(EventoRequest $request)
    {
        $evento = Evento::create($request->validated());

        return new EventoResource($evento);
    }

    public function show(Evento $evento)
    {
        return new EventoResource($evento);
    }

    public function update(EventoRequest $request, Evento $evento)
    {
        $evento->update($request->validated());

        return new EventoResource($evento);
    }

    public function destroy(Evento $evento)
    {
        $evento->delete();

        return response()->json(['mensagem' => 'Evento excluído com sucesso.']);
    }
}
