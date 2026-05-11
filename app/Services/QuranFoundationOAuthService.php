<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class QuranFoundationOAuthService
{
    public function getEnvironment(): string
    {
        $environment = strtolower((string) config('services.quran_foundation.environment', 'prelive'));

        return $environment === 'production' ? 'production' : 'prelive';
    }

    public function getAuthBaseUrl(): string
    {
        return $this->getEnvironment() === 'production'
            ? 'https://oauth2.quran.foundation'
            : 'https://prelive-oauth2.quran.foundation';
    }

    public function getGatewayBaseUrl(): string
    {
        return $this->getEnvironment() === 'production'
            ? 'https://apis.quran.foundation'
            : 'https://apis-prelive.quran.foundation';
    }

    public function getClientId(): string
    {
        return (string) config('services.quran_foundation.client_id', '');
    }

    public function getClientSecret(): string
    {
        return (string) config('services.quran_foundation.client_secret', '');
    }

    public function getRedirectUri(): string
    {
        return (string) config('services.quran_foundation.redirect_uri', '');
    }

    public function getUserScopes(): string
    {
        return trim((string) config('services.quran_foundation.user_scopes', 'openid offline_access user'));
    }

    public function generatePkceCodeVerifier(): string
    {
        return $this->base64UrlEncode(random_bytes(32));
    }

    public function generateState(): string
    {
        return $this->base64UrlEncode(random_bytes(32));
    }

    public function generateNonce(): string
    {
        return $this->base64UrlEncode(random_bytes(32));
    }

    public function createCodeChallenge(string $codeVerifier): string
    {
        return $this->base64UrlEncode(hash('sha256', $codeVerifier, true));
    }

    public function buildAuthorizationUrl(string $state, string $nonce, string $codeVerifier): string
    {
        $clientId = $this->getClientId();
        $redirectUri = $this->getRedirectUri();
        $scopes = $this->getUserScopes();

        if ($clientId === '' || $redirectUri === '') {
            throw new RuntimeException('Quran.Foundation OAuth client is not configured.');
        }

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scopes,
            'state' => $state,
            'nonce' => $nonce,
            'code_challenge' => $this->createCodeChallenge($codeVerifier),
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);

        return rtrim($this->getAuthBaseUrl(), '/') . '/oauth2/auth?' . $query;
    }

    /**
     * @return array<string, mixed>
     */
    public function exchangeAuthorizationCode(string $code, string $codeVerifier): array
    {
        $clientId = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $redirectUri = $this->getRedirectUri();

        if ($clientId === '' || $clientSecret === '' || $redirectUri === '') {
            throw new RuntimeException('Quran.Foundation token exchange is not configured.');
        }

        $response = Http::asForm()
            ->timeout(15)
            ->retry(1, 250)
            ->withBasicAuth($clientId, $clientSecret)
            ->post(rtrim($this->getAuthBaseUrl(), '/') . '/oauth2/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'code_verifier' => $codeVerifier,
                'client_id' => $clientId,
            ]);

        if (!$response->successful()) {
            $this->throwTokenExchangeException($response);
        }

        $tokenData = $response->json();
        if (!is_array($tokenData) || !isset($tokenData['access_token'])) {
            throw new RuntimeException('Token endpoint did not return an access token.');
        }

        return $tokenData;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function decodeIdToken(?string $idToken): ?array
    {
        if (!$idToken || substr_count($idToken, '.') < 2) {
            return null;
        }

        $parts = explode('.', $idToken);
        $payload = $parts[1] ?? '';

        if ($payload === '') {
            return null;
        }

        $decodedJson = $this->base64UrlDecode($payload);
        if ($decodedJson === '') {
            return null;
        }

        $decodedPayload = json_decode($decodedJson, true);

        return is_array($decodedPayload) ? $decodedPayload : null;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $padding = strlen($value) % 4;
        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        return is_string($decoded) ? $decoded : '';
    }

    private function throwTokenExchangeException(Response $response): void
    {
        $message = 'Failed to exchange authorization code for tokens.';
        $payload = $response->json();

        if (is_array($payload) && isset($payload['error_description'])) {
            $message .= ' ' . (string) $payload['error_description'];
        }

        throw new RuntimeException($message);
    }
}
