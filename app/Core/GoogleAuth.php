<?php
namespace App\Core;

/**
 * Lớp điều khiển luồng xác thực Google OAuth 2.0 (OpenID Connect)
 * Phục vụ Đăng ký & Đăng nhập một chạm bằng tài khoản Google.
 */
class GoogleAuth {
    private const AUTH_ENDPOINT     = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_ENDPOINT    = 'https://oauth2.googleapis.com/token';
    private const USERINFO_ENDPOINT = 'https://www.googleapis.com/oauth2/v3/userinfo';

    public static function isConfigured(): bool {
        $clientId = trim((string)env('GOOGLE_CLIENT_ID', ''));
        $clientSecret = trim((string)env('GOOGLE_CLIENT_SECRET', ''));
        return $clientId !== '' && $clientSecret !== '';
    }

    public static function getClientId(): string {
        return trim((string)env('GOOGLE_CLIENT_ID', ''));
    }

    public static function getClientSecret(): string {
        return trim((string)env('GOOGLE_CLIENT_SECRET', ''));
    }

    public static function getRedirectUri(): string {
        $configured = trim((string)env('GOOGLE_REDIRECT_URI', ''));
        if ($configured !== '') {
            return $configured;
        }
        return base_url('auth/google/callback');
    }

    /**
     * Tạo liên kết chuyển hướng người dùng sang trang cấp quyền của Google
     */
    public static function buildAuthUrl(string $state): string {
        $params = [
            'client_id'             => self::getClientId(),
            'redirect_uri'          => self::getRedirectUri(),
            'response_type'         => 'code',
            'scope'                 => 'openid email profile',
            'state'                 => $state,
            'access_type'           => 'online',
            'prompt'                => 'select_account',
            'include_granted_scopes'=> 'true',
        ];

        return self::AUTH_ENDPOINT . '?' . http_build_query($params);
    }

    /**
     * Đổi Authorization Code lấy Access Token từ Google
     *
     * @return array{access_token: string, expires_in?: int, id_token?: string}|null
     */
    public static function exchangeCodeForToken(string $code): ?array {
        $payload = [
            'code'          => $code,
            'client_id'     => self::getClientId(),
            'client_secret' => self::getClientSecret(),
            'redirect_uri'  => self::getRedirectUri(),
            'grant_type'    => 'authorization_code',
        ];

        $ch = curl_init(self::TOKEN_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            error_log("[GoogleAuth] Token exchange failed (HTTP {$httpCode}): " . (string)$response);
            return null;
        }

        $decoded = json_decode((string)$response, true);
        if (!is_array($decoded) || empty($decoded['access_token'])) {
            return null;
        }

        return $decoded;
    }

    /**
     * Lấy thông tin cá nhân từ Google API dựa trên Access Token
     *
     * @return array{sub: string, email: string, name?: string, picture?: string, email_verified?: bool}|null
     */
    public static function fetchUserInfo(string $accessToken): ?array {
        $ch = curl_init(self::USERINFO_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_HTTPGET        => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            error_log("[GoogleAuth] UserInfo request failed (HTTP {$httpCode}): " . (string)$response);
            return null;
        }

        $decoded = json_decode((string)$response, true);
        if (!is_array($decoded) || empty($decoded['sub']) || empty($decoded['email'])) {
            return null;
        }

        return $decoded;
    }
}
