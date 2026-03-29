<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthSessionController
{
    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => $this->transformUser($request->user()),
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'authenticated' => true,
            'user' => $this->transformUser($request->user()),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    protected function transformUser($user): array
    {
        $user->loadMissing(['roles:id,slug,name', 'outlet:id,name,slug']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => (bool) $user->email_verified_at,
            'roles' => $user->roles->pluck('slug')->values()->all(),
            'primary_role' => $user->roles->pluck('slug')->first(),
            'current_plan' => $user->currentPlan(),
            'remaining_orders' => $user->remainingOrders(),
            'outlet' => $user->outlet
                ? [
                    'id' => $user->outlet->id,
                    'name' => $user->outlet->name,
                    'slug' => $user->outlet->slug,
                ]
                : null,
        ];
    }
}
