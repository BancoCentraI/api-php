<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistroRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegistroRequest $request)
    {
        $user = User::create([
            'name' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->senha),
        ]);

        return response()->json([
            'usuario' => new UserResource($user),
            'token' => $user->createToken('api')->plainTextToken,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->senha, $user->password)) {
            return response()->json(['mensagem' => 'E-mail ou senha inválidos.'], 401);
        }

        return response()->json([
            'usuario' => new UserResource($user),
            'token' => $user->createToken('api')->plainTextToken,
        ]);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['mensagem' => 'Logout realizado com sucesso.']);
    }
}
