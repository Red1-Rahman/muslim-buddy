<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuranFoundationContentService
{
    public function __construct(
        private readonly QuranFoundationOAuthService $oauthService
    ) {
    }

    public function getChapterAudioUrl(int $reciterId, int $chapterNumber): ?string
    {
        $clientId = $this->oauthService->getClientId();
        $clientSecret = $this->oauthService->getClientSecret();

        if ($clientId === '' || $clientSecret === '') {
            return null;
        }

        $accessToken = $this->getContentAccessToken($clientId, $clientSecret);
        if (!$accessToken) {
            return null;
        }

        $baseUrl = rtrim($this->oauthService->getGatewayBaseUrl(), '/');
        $url = "{$baseUrl}/content/api/v4/chapter_recitations/{$reciterId}/{$chapterNumber}";

        try {
            $response = Http::timeout(10)
                ->retry(1, 250)
                ->withHeaders([
                    'x-auth-token' => $accessToken,
                    'x-client-id' => $clientId,
                    'Accept' => 'application/json',
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::warning('Quran.Foundation content audio request failed', [
                    'status' => $response->status(),
                    'chapter_number' => $chapterNumber,
                    'reciter_id' => $reciterId,
                ]);
                return null;
            }

            $audioUrl = data_get($response->json(), 'audio_file.audio_url');
            return is_string($audioUrl) && $audioUrl !== '' ? $audioUrl : null;
        } catch (\Throwable $e) {
            Log::warning('Quran.Foundation content audio request exception', [
                'error' => $e->getMessage(),
                'chapter_number' => $chapterNumber,
                'reciter_id' => $reciterId,
            ]);
            return null;
        }
    }

    private function getContentAccessToken(string $clientId, string $clientSecret): ?string
    {
        $cacheKey = 'qf_content_access_token_' . sha1($clientId . '|' . $this->oauthService->getEnvironment());

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($clientId, $clientSecret) {
            try {
                $response = Http::asForm()
                    ->timeout(10)
                    ->retry(1, 250)
                    ->withBasicAuth($clientId, $clientSecret)
                    ->post(rtrim($this->oauthService->getAuthBaseUrl(), '/') . '/oauth2/token', [
                        'grant_type' => 'client_credentials',
                        'scope' => 'content',
                    ]);

                if (!$response->successful()) {
                    Log::warning('Quran.Foundation content token request failed', [
                        'status' => $response->status(),
                    ]);
                    return null;
                }

                $token = data_get($response->json(), 'access_token');
                return is_string($token) && $token !== '' ? $token : null;
            } catch (\Throwable $e) {
                Log::warning('Quran.Foundation content token request exception', [
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }
}
