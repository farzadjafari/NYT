<?php

namespace App\Http\Controllers;

use App\Http\Requests\BestSellersRequest;
use App\Support\NytHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BestSellersController extends Controller
{
    private const NYT_API_ENDPOINT = 'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.nyt.api_key');
    }

    /**
     * Fetch NYT Best Sellers based on query parameters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(BestSellersRequest $request)
    {
        $validated = $request->validated();

        $validated['isbn'] = isset($validated['isbn']) ? implode(';', $validated['isbn']) : null;

        $cacheKey = NytHelper::generateCacheKey($validated);

        $shouldCache = !app()->isLocal();

        $response = $shouldCache ? Cache::remember($cacheKey, now()->addMinutes(10), function () use ($validated) {
            return NytHelper::fetchFromNytApi($validated, self::NYT_API_ENDPOINT, $this->apiKey);
        }) : NytHelper::fetchFromNytApi($validated, self::NYT_API_ENDPOINT, $this->apiKey);

        if (isset($response['error'])) {
            return response()->json(['error' => $response['error'], 'message' => $response['message']], 500);
        }

        return response()->json($response);
    }
}

