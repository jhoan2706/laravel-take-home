<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Remix\RemixService;
use Illuminate\Http\Request;

class RemixController extends Controller
{
    public function store(Request $request, RemixService $remixService)
    {
        // Validate incoming text
        $validated = $request->validate([
            'text' => 'required|string|min:20|max:280',
        ]);

        // Generate 4 unique variants using RemixService
        $variants = $remixService->variants($validated['text']);

        // Return the variants as JSON
        return response()->json([
            'variants' => $variants,
        ]);
    }
}
