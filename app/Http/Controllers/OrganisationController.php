<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganisationController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $organisation = Organisation::create([
            'nom' => $validated['nom'],
            'slug' => Str::slug($validated['nom']),
        ]);

        $admin = $organisation->admins()->create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Organisation créée avec succès',
            'organisation' => [
                'id' => $organisation->id,
                'nom' => $organisation->nom,
                'slug' => $organisation->slug,
            ],
        ], 201);
    }
}
