<?php

namespace App\Http\Controllers\Apis\v1;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\Photo;
use App\Models\TechnicalVisits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PhotoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'intervention_id' => ['nullable', 'integer', 'exists:interventions,id'],
            'technical_visit_id' => ['nullable', 'integer', 'exists:technical_visits,id'],
            'data' => ['required', 'string'],
            'filename' => ['required', 'string'],
        ]);

        if (empty($validated['intervention_id']) && empty($validated['technical_visit_id'])) {
            throw ValidationException::withMessages([
                'intervention_id' => 'Une photo doit être rattachée à une intervention ou à une visite technique.',
            ]);
        }

        if (!empty($validated['intervention_id'])) {
            $belongs = Intervention::query()
                ->whereKey($validated['intervention_id'])
                ->whereHas('interventionTechniciens', fn ($q) => $q->where('technicien_id', $user->technicien_id))
                ->exists();

            if (!$belongs) {
                return response()->json([
                    'success' => false,
                    'message' => "Cette intervention ne vous est pas assignée.",
                ], 403);
            }
        }

        if (!empty($validated['technical_visit_id'])) {
            $belongs = TechnicalVisits::query()
                ->whereKey($validated['technical_visit_id'])
                ->where('user_id', $user->id)
                ->exists();

            if (!$belongs) {
                return response()->json([
                    'success' => false,
                    'message' => "Cette visite technique ne vous est pas attribuée.",
                ], 403);
            }
        }

        $base64 = $validated['data'];
        if (str_contains($base64, ',') && str_starts_with($base64, 'data:')) {
            $base64 = substr($base64, strpos($base64, ',') + 1);
        }

        $binary = base64_decode($base64, true);
        if ($binary === false) {
            throw ValidationException::withMessages([
                'data' => "Le contenu de la photo n'est pas un base64 valide.",
            ]);
        }

        $extension = pathinfo($validated['filename'], PATHINFO_EXTENSION) ?: 'jpg';
        $storedName = Str::uuid() . '.' . $extension;
        $path = "photos/{$storedName}";

        Storage::disk('public')->put($path, $binary);

        $photo = Photo::create([
            'intervention_id' => $validated['intervention_id'] ?? null,
            'technical_visit_id' => $validated['technical_visit_id'] ?? null,
            'filename' => $validated['filename'],
            'path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Photo enregistrée avec succès',
            'data' => [
                'id' => $photo->id,
                'intervention_id' => $photo->intervention_id,
                'technical_visit_id' => $photo->technical_visit_id,
                'filename' => $photo->filename,
                'url' => $photo->url(),
            ],
        ]);
    }
}
