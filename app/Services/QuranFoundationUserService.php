<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class QuranFoundationUserService
{
    public function __construct(
        private readonly QuranFoundationOAuthService $oauthService
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchUserProfile(string $accessToken, bool $includeQdc = true): array
    {
        $clientId = $this->oauthService->getClientId();
        if ($clientId === '') {
            throw new RuntimeException('Quran.Foundation client ID is missing.');
        }

        $query = $includeQdc ? ['qdc' => 'true'] : [];
        $baseUrl = rtrim($this->oauthService->getGatewayBaseUrl(), '/');
        $url = $baseUrl . '/quran-reflect/v1/users/profile';

        $response = Http::timeout(10)
            ->retry(1, 250)
            ->withHeaders([
                'x-auth-token' => $accessToken,
                'x-client-id' => $clientId,
                'Accept' => 'application/json',
            ])
            ->get($url, $query);

        if (!$response->successful()) {
            $status = $response->status();
            throw new RuntimeException("Failed to fetch Quran.Foundation user profile (HTTP {$status}).");
        }

        $data = $response->json();
        if (!is_array($data)) {
            throw new RuntimeException('User profile response format was invalid.');
        }

        return $data;
    }
}
