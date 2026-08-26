<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $validated['email'])->first();

        if (!$admin || !\Hash::check($validated['password'], $admin->password)) {
            return response()->json([
                'error' => 'Identifiants invalides',
            ], 401);
        }

        Auth::login($admin);

        $admin->load('organisation');

        return response()->json([
            'admin' => [
                'id' => $admin->id,
                'email' => $admin->email,
            ],
            'organisation' => [
                'id' => $admin->organisation->id,
                'nom' => $admin->organisation->nom,
                'slug' => $admin->organisation->slug,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Déconnecté avec succès',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $admin = Auth::user();

        if (!$admin) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $admin->load('organisation');

        return response()->json([
            'admin' => [
                'id' => $admin->id,
                'email' => $admin->email,
            ],
            'organisation' => [
                'id' => $admin->organisation->id,
                'nom' => $admin->organisation->nom,
                'slug' => $admin->organisation->slug,
            ],
        ]);
    }
}
