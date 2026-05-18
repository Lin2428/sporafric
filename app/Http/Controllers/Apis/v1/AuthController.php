<?php

namespace App\Http\Controllers\Apis\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @authenticated
 * @header Authorization Bearer {token}
 */
class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('phone', $validated['phone'])
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Numero de téléphone ou mot de passe incorrect.',
            ]);
        }

        if (! $user->status) {
            return response()->json([
                'success' => false,
                'message' => 'Compte inactif.',
            ]);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Authentification reussie.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ],
        ]);
    }
}
