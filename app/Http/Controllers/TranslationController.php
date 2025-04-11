<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'tags', 'locale']);
        $translations = Translation::withFilters($filters)
            ->with(['locale', 'tags'])
            ->paginate(20);

        return response()->json([
            'message' => 'Translations fetched successfully',
            'success' => true,
            'data' => $translations
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'locale_id' => 'required|exists:locales,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $translation = Translation::create($validated);

        if (isset($validated['tags'])) {
            $translation->tags()->sync($validated['tags']);
        }

        Cache::forget('translations_export');

        return response()->json([
            'message' => 'Translation created successfully',
            'success' => true,
            'data' => $translation->load('locale', 'tags'),
        ], 201);
    }

    public function show(Translation $translation)
    {
        return response()->json($translation->load('locale', 'tags'));
    }

    public function update(Request $request, Translation $translation)
    {
        $validated = $request->validate([
            'group' => 'sometimes|string|max:255',
            'key' => 'sometimes|string|max:255',
            'value' => 'sometimes|string',
            'locale_id' => 'sometimes|exists:locales,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $translation->update($validated);

        if (isset($validated['tags'])) {
            $translation->tags()->sync($validated['tags']);
        }

        Cache::forget('translations_export');

        return response()->json([
            'message' => 'Translation updated successfully',
            'success' => true,
            'data' => $translation->load('locale', 'tags'),
        ], 200);
    }

    public function destroy(Translation $translation)
    {
        $translation->delete();
        Cache::forget('translations_export');
        return response()->json(null, 204);
    }

    public function export()
    {
        return Cache::remember('translations_export', now()->addHour(), function () {
            $translations = Translation::with(['locale', 'tags'])
                ->get()
                ->groupBy('locale.code')
                ->map(function ($translations) {
                    return $translations->groupBy('group')
                        ->map(function ($groupTranslations) {
                            return $groupTranslations->mapWithKeys(function ($translation) {
                                return [$translation->key => $translation->value];
                            });
                        });
                });

            return response()->json($translations);
        });
    }
}
