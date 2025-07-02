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
}
