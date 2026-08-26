<?php

namespace App\Http\Controllers;

use App\Models\Collaborateur;
use App\Models\OtpCode;
use App\Models\Organisation;
use App\Models\Pointage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PointageController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $org = Organisation::where('slug', $slug)->first();
        if (!$org) {
            return response()->json(['error' => 'Organisation introuvable.'], 404);
        }

        $collab = $this->verifiedCollaborateur($org);

        $alreadyPointed = false;
        if ($collab) {
            $alreadyPointed = Pointage::where('collaborateur_id', $collab->id)
                ->where('date', now()->toDateString())
                ->exists();
        }

        return response()->json([
            'collaborateur' => $collab ? [
                'id' => $collab->id,
                'nom' => $collab->nom,
                'email' => $collab->email,
            ] : null,
            'alreadyPointed' => $alreadyPointed,
        ]);
    }

    public function requestOtp(Request $request, string $slug): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $org = Organisation::where('slug', $slug)->first();
        if (!$org) {
            return response()->json(['error' => 'Email introuvable ou organisation invalide.'], 422);
        }

        $collab = Collaborateur::where('organisation_id', $org->id)
            ->where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$collab) {
            return response()->json(['error' => 'Email introuvable ou organisation invalide.'], 422);
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'collaborateur_id' => $collab->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw(
            "Votre code de verification pour SuiviProj est : {$code}\n\nCe code expire dans 10 minutes.",
            function ($message) use ($collab) {
                $message->to($collab->email)
                    ->subject('Code de verification - SuiviProj');
            }
        );

        return response()->json(['message' => 'Code envoye par email.']);
    }

    public function verifyOtp(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $org = Organisation::where('slug', $slug)->first();
        if (!$org) {
            return response()->json(['error' => 'Organisation invalide.'], 422);
        }

        $collab = Collaborateur::where('organisation_id', $org->id)
            ->where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$collab) {
            return response()->json(['error' => 'Email introuvable.'], 422);
        }

        $otp = OtpCode::where('collaborateur_id', $collab->id)
            ->where('code', $request->code)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json(['error' => 'Code invalide ou expire.'], 422);
        }

        $otp->update(['consumed_at' => now()]);

        session(["pointage_collab_{$org->id}" => $collab->id]);

        return response()->json([
            'collaborateur' => [
                'id' => $collab->id,
                'nom' => $collab->nom,
                'email' => $collab->email,
            ],
        ]);
    }

    public function storeRapport(Request $request, string $slug): JsonResponse
    {
        $org = Organisation::where('slug', $slug)->first();
        if (!$org) {
            return response()->json(['error' => 'Organisation invalide.'], 422);
        }

        $collab = $this->verifiedCollaborateur($org);
        if (!$collab) {
            return response()->json(['error' => 'Session invalide.'], 401);
        }

        $alreadyPointed = Pointage::where('collaborateur_id', $collab->id)
            ->where('date', now()->toDateString())
            ->exists();

        if ($alreadyPointed) {
            return response()->json(['error' => 'Vous avez deja valide votre pointage aujourd\'hui.'], 422);
        }

        $request->validate([
            'contenu_fait' => 'required|string',
            'blocage' => 'nullable|string',
        ]);

        Pointage::create([
            'collaborateur_id' => $collab->id,
            'date' => now()->toDateString(),
            'contenu_fait' => $request->contenu_fait,
            'blocage' => $request->blocage,
        ]);

        return response()->json(['message' => 'Pointage enregistre.']);
    }

    private function verifiedCollaborateur(Organisation $org): ?Collaborateur
    {
        $collabId = session("pointage_collab_{$org->id}");
        if (!$collabId) {
            return null;
        }

        return Collaborateur::where('id', $collabId)
            ->where('organisation_id', $org->id)
            ->where('is_active', true)
            ->first();
    }
}
