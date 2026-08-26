<?php

namespace App\Http\Controllers;

use App\Models\Collaborateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollaborateurController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $organisationId = $request->user()->organisation_id;

        $collaborateurs = Collaborateur::where('organisation_id', $organisationId)
            ->orderBy('email')
            ->get();

        return response()->json($collaborateurs);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'nom' => 'nullable|string|max:255',
        ]);

        $organisationId = $request->user()->organisation_id;

        $collaborateur = Collaborateur::create([
            'organisation_id' => $organisationId,
            'email' => $validated['email'],
            'nom' => $validated['nom'] ?? null,
        ]);

        return response()->json($collaborateur, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $organisationId = $request->user()->organisation_id;

        $collaborateur = Collaborateur::where('organisation_id', $organisationId)
            ->findOrFail($id);

        $validated = $request->validate([
            'is_active' => 'sometimes|boolean',
            'nom' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|email|max:255',
        ]);

        $collaborateur->update($validated);

        return response()->json($collaborateur);
    }
}
