<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Auth\UseCases\GetMeUseCase;
use App\Application\Auth\UseCases\LoginUseCase;
use App\Application\Auth\UseCases\LogoutUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use Illuminate\Http\JsonResponse;

final class AuthController extends Controller
{
    public function login(LoginRequest $request, LoginUseCase $login): JsonResponse
    {
        $data = $login->execute(
            email: (string) $request->validated('email'),
            password: (string) $request->validated('password'),
            tokenName: (string) ($request->validated('device_name') ?? 'api'),
        );

        return response()->json([
            'token' => $data['token'],
            'user' => [
                'id' => $data['user']->id,
                'name' => $data['user']->name,
                'email' => $data['user']->email,
                'role' => $data['user']->role->value,
            ],
        ]);
    }

    public function logout(LogoutUseCase $logout): JsonResponse
    {
        $logout->execute();

        return response()->json([
            'message' => 'Sesión cerrada.',
        ]);
    }

    public function me(GetMeUseCase $me): JsonResponse
    {
        $user = $me->execute();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
            ],
        ]);
    }
}

