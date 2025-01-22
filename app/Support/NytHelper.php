<?php

namespace App\Support;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class NytHelper
{
    /**
     * Fetch data from the NYT API.
     *
     * @param array $validated
     * @return array
     */
    public static function fetchFromNytApi(array $validated, $endpoint, $apiKey): array
    {
        try {
            return Http::get($endpoint, array_merge($validated, ['api-key' => $apiKey]))->throw()->json();
        } catch (RequestException $exception) {
            return ['error' => 'Failed to fetch data from NYT API.', 'message' => $exception->getMessage()];
        }
    }

    /**
     * Generate a unique cache key for the query.
     *
     * @param array $query
     * @return string
     */
    public static function generateCacheKey(array $query): string
    {
        return 'nyt_best_sellers_' . md5(json_encode($query));
    }
}
