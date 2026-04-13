<?php

namespace App\Http\Controllers;

use App\Services\OgpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OgpController extends Controller
{
    public function show(Request $request, OgpService $ogpService): JsonResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'string', 'max:2048'],
        ]);

        $data = $ogpService->fetch($validated['url']);

        return response()->json([
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? null,
            'url' => $data['url'] ?? $validated['url'],
            'site_name' => $data['site_name'] ?? '',
        ]);
    }
}
