<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class TypesenseController extends Controller
{
    public function getScopedKey(Request $request): JsonResponse
    {
        $searchOnlyKey = config('scout.typesense.client-settings.search_only_key');
        
        if (empty($searchOnlyKey)) {
            return response()->json(['error' => 'Search service configuration error'], 500);
        }

        return response()->json(['apiKey' => $searchOnlyKey]);
    }
    

    public function multiSearch(Request $request): JsonResponse
    {
        $typesenseHost = config('scout.typesense.client-settings.nodes.0.host');
        $typesensePort = config('scout.typesense.client-settings.nodes.0.port');
        $typesenseProtocol = config('scout.typesense.client-settings.nodes.0.protocol');
        $typesenseApiKey = config('scout.typesense.client-settings.search_only_key');

        if (empty($typesenseApiKey)) {
            return response()->json(['error' => 'Search service configuration error'], 500);
        }

        $response = Http::withHeaders([
            'X-TYPESENSE-API-KEY' => $typesenseApiKey,
            'Content-Type' => 'application/json'
        ])->post("{$typesenseProtocol}://{$typesenseHost}:{$typesensePort}/multi_search", $request->all());

        if (!$response->successful()) {
            return response()->json(['error' => 'Search service error'], 500);
        }

        return response()->json($response->json());
    }
}
