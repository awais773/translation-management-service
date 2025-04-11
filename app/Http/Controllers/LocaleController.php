<?php

namespace App\Http\Controllers;

use App\Models\Locale;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function index()
    {
        return response()->json(Locale::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:locales',
            'name' => 'required|string|max:255'
        ]);

        $locale = Locale::create($validated);

        return response()->json($locale, 201);
    }
}