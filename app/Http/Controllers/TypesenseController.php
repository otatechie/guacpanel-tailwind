<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TypesenseController extends Controller
{
    private const ALLOWED_COLLECTIONS = ['users', 'financial_metrics'];

    public function getScopedKey(Request $request): JsonResponse
    {
        $searchOnlyKey = config('scout.typesense.client-settings.search_only_key');
        
        if (empty($searchOnlyKey)) {
            Log::error('Typesense search-only key not configured');
            return response()->json(['error' => 'Search service configuration error'], 500);
        }

        // Get collections from request or use default
        $collections = array_intersect(
            $request->get('collections', ['users']), 
            self::ALLOWED_COLLECTIONS
        );
        
        if (empty($collections)) {
            return response()->json(['error' => 'No valid collections specified'], 400);
        }

        return response()->json(['apiKey' => $searchOnlyKey]);
    }

    
    public function multiSearch(Request $request): JsonResponse
    {
        $typesenseHost = config('scout.typesense.client-settings.nodes.0.host');
        $typesensePort = config('scout.typesense.client-settings.nodes.0.port');
        $typesenseProtocol = config('scout.typesense.client-settings.nodes.0.protocol');
        $typesenseApiKey = config('scout.typesense.client-settings.api_key');

        if (empty($typesenseApiKey)) {
            Log::error('Typesense API key not configured');
            return response()->json(['error' => 'Search service configuration error'], 500);
        }

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post("{$typesenseProtocol}://{$typesenseHost}:{$typesensePort}/multi_search", [
                'headers' => [
                    'X-TYPESENSE-API-KEY' => $typesenseApiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => $request->all()
            ]);

            return response()->json(json_decode($response->getBody()->getContents()));
        } catch (\Exception $e) {
            Log::error('Typesense multi-search error: ' . $e->getMessage());
            return response()->json(['error' => 'Search service error'], 500);
        }
    }
}
