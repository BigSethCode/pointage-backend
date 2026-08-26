<?php

namespace App\Http\Controllers;

use App\Models\Collaborateur;
use App\Models\Pointage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RapportController extends Controller
{
    public function aujourdhui(Request $request): JsonResponse
    {
        $organisationId = $request->user()->organisation_id;
        $date = $request->query('date', now()->format('Y-m-d'));

        $activeCollaborateurs = Collaborateur::where('organisation_id', $organisationId)
            ->where('is_active', true)
            ->get();

        $pointageIds = Pointage::where('date', $date)
            ->whereIn('collaborateur_id', $activeCollaborateurs->pluck('id'))
            ->pluck('collaborateur_id');

        $reports = Pointage::where('date', $date)
            ->whereIn('collaborateur_id', $activeCollaborateurs->pluck('id'))
            ->with('collaborateur')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'collaborateur' => [
                    'id' => $p->collaborateur->id,
                    'email' => $p->collaborateur->email,
                    'nom' => $p->collaborateur->nom,
                ],
                'contenu_fait' => $p->contenu_fait,
                'blocage' => $p->blocage,
                'created_at' => $p->created_at,
            ]);

        $missingMembers = $activeCollaborateurs
            ->filter(fn ($c) => !$pointageIds->contains($c->id))
            ->map(fn ($c) => [
                'id' => $c->id,
                'email' => $c->email,
                'nom' => $c->nom,
            ])
            ->values();

        return response()->json([
            'reports' => $reports,
            'missingMembers' => $missingMembers,
        ]);
    }
}
