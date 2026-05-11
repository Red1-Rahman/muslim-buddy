<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\QuranFoundationOAuthService;
use App\Services\QuranFoundationUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class QuranFoundationAuthController extends Controller
{
    public function __construct(
        private readonly QuranFoundationOAuthService $oauthService,
        private readonly QuranFoundationUserService $userService
    ) {
    }

    public function redirect(Request $request): RedirectResponse
    {
        try {
            $codeVerifier = $this->oauthService->generatePkceCodeVerifier();
            $state = $this->oauthService->generateState();
            $nonce = $this->oauthService->generateNonce();

            $request->session()->put('qf_oauth', [
                'state' => $state,
                'nonce' => $nonce,
                'code_verifier' => $codeVerifier,
                'link_user_id' => Auth::id(),
                'redirect_uri' => $this->oauthService->getRedirectUri(),
            ]);

            $authorizationUrl = $this->oauthService->buildAuthorizationUrl($state, $nonce, $codeVerifier);

            return redirect()->away($authorizationUrl);
        } catch (\Throwable $e) {
            Log::error('Quran.Foundation OAuth redirect failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route(Auth::check() ? 'profile.show' : 'login')->withErrors([
                'email' => 'Quran.Foundation sign-in is not configured yet.',
            ]);
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        $oauthSession = $request->session()->pull('qf_oauth');
        $failureRoute = Auth::check() ? 'profile.show' : 'login';

        if (is_array($oauthSession) && (int) ($oauthSession['link_user_id'] ?? 0) > 0) {
            $failureRoute = 'profile.show';
        }

        if (!is_array($oauthSession)) {
            return redirect()->route($failureRoute)->withErrors([
                'email' => 'Your Quran.Foundation session expired. Please try again.',
            ]);
        }

        $incomingState = (string) $request->query('state', '');
        $expectedState = (string) ($oauthSession['state'] ?? '');
        if ($incomingState === '' || $incomingState !== $expectedState) {
            return redirect()->route($failureRoute)->withErrors([
                'email' => 'OAuth state mismatch. Please try again.',
            ]);
        }

        $authCode = (string) $request->query('code', '');
        if ($authCode === '') {
            $error = (string) $request->query('error_description', $request->query('error', 'Authorization failed.'));
            return redirect()->route($failureRoute)->withErrors(['email' => $error]);
        }

        $codeVerifier = (string) ($oauthSession['code_verifier'] ?? '');
        if ($codeVerifier === '') {
            return redirect()->route($failureRoute)->withErrors([
                'email' => 'PKCE verifier missing. Please retry sign-in.',
            ]);
        }

        try {
            $tokenData = $this->oauthService->exchangeAuthorizationCode($authCode, $codeVerifier);
        } catch (\Throwable $e) {
            Log::error('Quran.Foundation OAuth token exchange failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route($failureRoute)->withErrors([
                'email' => 'Failed to exchange authorization code for tokens.',
            ]);
        }

        $idTokenPayload = $this->oauthService->decodeIdToken((string) data_get($tokenData, 'id_token', ''));
        $expectedNonce = (string) ($oauthSession['nonce'] ?? '');
        $tokenNonce = is_array($idTokenPayload) ? (string) data_get($idTokenPayload, 'nonce', '') : '';

        if ($expectedNonce !== '' && $tokenNonce !== '' && $expectedNonce !== $tokenNonce) {
            return redirect()->route($failureRoute)->withErrors([
                'email' => 'OAuth nonce validation failed. Please try again.',
            ]);
        }

        try {
            $user = $this->resolveLocalUser($oauthSession, $idTokenPayload, $tokenData);
            Auth::login($user, true);
            $request->session()->regenerate();

            $this->storeSessionTokens($request, $tokenData, $idTokenPayload);
            $this->syncQuranFoundationProfile($request, $user, (string) data_get($tokenData, 'access_token', ''));
        } catch (\Throwable $e) {
            Log::error('Quran.Foundation user mapping failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route($failureRoute)->withErrors([
                'email' => $e instanceof RuntimeException
                    ? $e->getMessage()
                    : 'Unable to connect this Quran.Foundation account right now.',
            ]);
        }

        return redirect()->intended(route('profile.show'))->with(
            'success',
            'Connected with Quran.Foundation successfully.'
        );
    }

    public function sync(Request $request): RedirectResponse
    {
        $oauthData = $request->session()->get('qf_tokens');
        if (!is_array($oauthData) || !isset($oauthData['access_token'])) {
            return redirect()->route('profile.show')->withErrors([
                'email' => 'No Quran.Foundation token found in this session. Please connect again.',
            ]);
        }

        $accessToken = (string) $oauthData['access_token'];
        $this->syncQuranFoundationProfile($request, $request->user(), $accessToken);

        return redirect()->route('profile.show')->with('success', 'Quran.Foundation profile synced.');
    }

    public function disconnect(Request $request): RedirectResponse
    {
        $request->session()->forget('qf_tokens');
        $request->session()->forget('qf_profile');

        return redirect()->route('profile.show')->with('success', 'Disconnected Quran.Foundation session.');
    }

    /**
     * @param array<string, mixed> $oauthSession
     * @param array<string, mixed>|null $idTokenPayload
     * @param array<string, mixed> $tokenData
     */
    private function resolveLocalUser(array $oauthSession, ?array $idTokenPayload, array $tokenData): User
    {
        $linkUserId = (int) ($oauthSession['link_user_id'] ?? 0);
        $qfSub = is_array($idTokenPayload) ? (string) data_get($idTokenPayload, 'sub', '') : '';
        $email = is_array($idTokenPayload) ? (string) data_get($idTokenPayload, 'email', '') : '';
        $firstName = is_array($idTokenPayload) ? (string) data_get($idTokenPayload, 'first_name', '') : '';
        $lastName = is_array($idTokenPayload) ? (string) data_get($idTokenPayload, 'last_name', '') : '';

        if ($qfSub === '' || $email === '') {
            $userInfo = $this->tryFetchUserInfoFromAccessToken($tokenData);
            $userinfoSub = (string) data_get($userInfo, 'sub', '');
            $userinfoEmail = (string) data_get($userInfo, 'email', '');

            if ($qfSub === '' && $userinfoSub !== '') {
                $qfSub = $userinfoSub;
            }

            if ($userinfoEmail !== '' && $email === '') {
                $email = $userinfoEmail;
            }
        }

        $displayName = trim($firstName . ' ' . $lastName);
        if ($displayName === '') {
            $displayName = $email !== '' ? strtok($email, '@') : 'Quran Foundation User';
        }

        if ($linkUserId > 0) {
            $linkedUser = User::find($linkUserId);
            if ($linkedUser) {
                $this->updateUserQfLink($linkedUser, $qfSub, $email, $displayName);
                return $linkedUser;
            }
        }

        $user = null;
        if ($qfSub !== '') {
            $safeSub = str_replace("'", "''", $qfSub);
            $user = User::whereRaw("qf_user_id = '{$safeSub}'")->first();
        }

        if (!$user && $email !== '') {
            $safeEmail = str_replace("'", "''", $email);
            $user = User::whereRaw("email = '{$safeEmail}'")->first();
        }

        if (!$user) {
            $generatedEmail = $email !== '' ? $email : ('qf-' . Str::uuid() . '@quran.foundation.local');
            $user = User::create([
                'name' => $displayName,
                'email' => $generatedEmail,
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(40)),
                'qf_user_id' => $qfSub !== '' ? $qfSub : null,
                'qf_email' => $email !== '' ? $email : null,
            ]);
        } else {
            $this->updateUserQfLink($user, $qfSub, $email, $displayName);
        }

        return $user;
    }

    private function updateUserQfLink(User $user, string $qfSub, string $email, string $displayName): void
    {
        $updates = [];

        if ($qfSub !== '' && $user->qf_user_id !== $qfSub) {
            $safeSub = str_replace("'", "''", $qfSub);
            $alreadyLinkedUser = User::whereRaw("qf_user_id = '{$safeSub}'")->first();
            if ($alreadyLinkedUser && (int) $alreadyLinkedUser->id !== (int) $user->id) {
                throw new RuntimeException('This Quran.Foundation account is already linked to another user.');
            }

            $updates['qf_user_id'] = $qfSub;
        }

        if ($email !== '' && $user->qf_email !== $email) {
            $updates['qf_email'] = $email;
        }

        if ($user->name === 'Google User' || $user->name === 'Quran Foundation User') {
            $updates['name'] = $displayName;
        }

        if (!empty($updates)) {
            $user->update($updates);
        }
    }

    /**
     * @param array<string, mixed> $tokenData
     * @return array<string, mixed>|null
     */
    private function tryFetchUserInfoFromAccessToken(array $tokenData): ?array
    {
        $accessToken = (string) data_get($tokenData, 'access_token', '');
        if ($accessToken === '') {
            return null;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->retry(1, 250)
                ->withToken($accessToken)
                ->get(rtrim($this->oauthService->getAuthBaseUrl(), '/') . '/userinfo');

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            return is_array($data) ? $data : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $tokenData
     * @param array<string, mixed>|null $idTokenPayload
     */
    private function storeSessionTokens(Request $request, array $tokenData, ?array $idTokenPayload): void
    {
        $expiresIn = (int) data_get($tokenData, 'expires_in', 3600);
        $expiresAt = now()->addSeconds(max($expiresIn - 30, 60))->toIso8601String();

        $request->session()->put('qf_tokens', [
            'access_token' => (string) data_get($tokenData, 'access_token', ''),
            'refresh_token' => (string) data_get($tokenData, 'refresh_token', ''),
            'id_token' => (string) data_get($tokenData, 'id_token', ''),
            'scope' => (string) data_get($tokenData, 'scope', ''),
            'token_type' => (string) data_get($tokenData, 'token_type', 'bearer'),
            'expires_at' => $expiresAt,
            'sub' => is_array($idTokenPayload) ? (string) data_get($idTokenPayload, 'sub', '') : '',
        ]);
    }

    private function syncQuranFoundationProfile(Request $request, User $user, string $accessToken): void
    {
        if ($accessToken === '') {
            return;
        }

        try {
            $profile = $this->userService->fetchUserProfile($accessToken, true);
            $request->session()->put('qf_profile', $profile);

            $updates = [
                'qf_profile_synced_at' => now(),
            ];

            $profileId = (string) data_get($profile, 'id', data_get($profile, 'userId', ''));
            if ($profileId !== '' && empty($user->qf_user_id)) {
                $updates['qf_user_id'] = $profileId;
            }

            $profileEmail = (string) data_get($profile, 'email', '');
            if ($profileEmail !== '') {
                $updates['qf_email'] = $profileEmail;
            }

            $photoUrl = (string) data_get($profile, 'photoUrl', data_get($profile, 'photo_url', ''));
            if ($photoUrl !== '' && empty($user->avatar)) {
                $updates['avatar'] = $photoUrl;
            }

            $firstName = (string) data_get($profile, 'firstName', data_get($profile, 'first_name', ''));
            $lastName = (string) data_get($profile, 'lastName', data_get($profile, 'last_name', ''));
            $fullName = trim($firstName . ' ' . $lastName);
            if ($fullName !== '' && ($user->name === 'Quran Foundation User' || $user->name === 'Google User')) {
                $updates['name'] = $fullName;
            }

            $user->update($updates);
        } catch (\Throwable $e) {
            Log::warning('Unable to sync Quran.Foundation user profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
