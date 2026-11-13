<?php
namespace App\Core;

/**
 * TOTP (RFC 6238 / RFC 4648) + Mã dự phòng 2FA kiểu Discord + Mã hóa secret lưu DB.
 */
class Totp {
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const BACKUP_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    private const PERIOD_SECONDS  = 30;
    private const DIGITS          = 6;

    /**
     * Sinh secret Base32 ngẫu nhiên (160-bit = 32 ký tự Base32).
     */
    public static function generateSecret(int $byteLength = 20): string {
        $bytes = random_bytes($byteLength);
        return self::base32Encode($bytes);
    }

    /**
     * Tạo URI otpauth:// chuẩn để quét bằng Google Authenticator / Authy.
     */
    public static function getOtpAuthUri(string $issuer, string $accountEmail, string $secret): string {
        $cleanIssuer = rawurlencode($issuer);
        $cleanLabel  = rawurlencode($issuer . ':' . $accountEmail);
        return "otpauth://totp/{$cleanLabel}?secret={$secret}&issuer={$cleanIssuer}&algorithm=SHA1&digits=" . self::DIGITS . '&period=' . self::PERIOD_SECONDS;
    }

    /**
     * Tính mã OTP 6 chữ số tại mốc timeSlice (mỗi slice = 30 giây).
     */
    public static function getCode(string $secret, ?int $timeSlice = null): string {
        $slice = $timeSlice ?? (int)floor(time() / self::PERIOD_SECONDS);
        $binaryKey = self::base32Decode($secret);
        if ($binaryKey === '') {
            return '';
        }

        // Counter 64-bit big-endian (4 byte cao = 0 với thời gian hiện đại)
        $timeBytes = pack('N*', 0, $slice);
        $hash = hash_hmac('sha1', $timeBytes, $binaryKey, true);

        $offset = ord($hash[19]) & 0x0F;
        $truncated = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $otp = $truncated % (10 ** self::DIGITS);
        return str_pad((string)$otp, self::DIGITS, '0', STR_PAD_LEFT);
    }

    /**
     * Xác thực mã 6 số với độ lệch ±discrepancy slice và chặn dùng lại OTP cùng/cũ slice.
     */
    public static function verifyCode(
        string $secret,
        string $code,
        int $discrepancy = 1,
        ?int $lastUsedSlice = null,
        ?int &$matchedSlice = null
    ): bool {
        $cleanCode = preg_replace('/\s+/', '', trim($code));
        if (!is_string($cleanCode) || preg_match('/^\d{' . self::DIGITS . '}$/', $cleanCode) !== 1) {
            return false;
        }

        $currentSlice = (int)floor(time() / self::PERIOD_SECONDS);
        for ($offset = -$discrepancy; $offset <= $discrepancy; $offset++) {
            $slice = $currentSlice + $offset;
            if ($lastUsedSlice !== null && $slice <= $lastUsedSlice) {
                continue;
            }
            $expected = self::getCode($secret, $slice);
            if ($expected !== '' && hash_equals($expected, $cleanCode)) {
                $matchedSlice = $slice;
                return true;
            }
        }

        return false;
    }

    /**
     * Sinh danh sách mã dự phòng 8 ký tự dễ đọc dạng XXXX-XXXX (chuẩn kiểu Discord).
     *
     * @return array<int, string>
     */
    public static function generateBackupCodes(int $count = 10): array {
        $alphabetLen = strlen(self::BACKUP_ALPHABET);
        $codes = [];
        while (count($codes) < $count) {
            $raw = '';
            for ($i = 0; $i < 8; $i++) {
                $raw .= self::BACKUP_ALPHABET[random_int(0, $alphabetLen - 1)];
            }
            $formatted = substr($raw, 0, 4) . '-' . substr($raw, 4, 4);
            if (!in_array($formatted, $codes, true)) {
                $codes[] = $formatted;
            }
        }
        return $codes;
    }

    /**
     * Chuẩn hóa mã dự phòng khi người dùng nhập (không phân biệt hoa/thường và dấu gạch).
     */
    public static function normalizeBackupCode(string $code): string {
        $stripped = preg_replace('/[^A-Za-z0-9]/', '', trim($code));
        return strtoupper((string)$stripped);
    }

    /**
     * Mã hóa TOTP secret trước khi lưu DB bằng AES-256-GCM nếu APP_KEY được cấu hình.
     */
    public static function encryptSecret(string $plainSecret): string {
        $appKey = self::resolveEncryptionKey();
        if ($appKey === null || !function_exists('openssl_encrypt')) {
            return $plainSecret;
        }
        $iv = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt($plainSecret, 'aes-256-gcm', $appKey, OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false || strlen($tag) !== 16) {
            return $plainSecret;
        }
        return 'enc:v1:' . base64_encode($iv . $tag . $cipher);
    }

    /**
     * Giải mã TOTP secret đọc từ DB.
     */
    public static function decryptSecret(?string $stored): ?string {
        if ($stored === null || $stored === '') {
            return null;
        }
        if (strpos($stored, 'enc:v1:') !== 0) {
            return $stored;
        }
        $appKey = self::resolveEncryptionKey();
        if ($appKey === null || !function_exists('openssl_decrypt')) {
            return null;
        }
        $raw = base64_decode(substr($stored, 7), true);
        if ($raw === false || strlen($raw) <= 28) {
            return null;
        }
        $iv = substr($raw, 0, 12);
        $tag = substr($raw, 12, 16);
        $cipher = substr($raw, 28);
        $plain = openssl_decrypt($cipher, 'aes-256-gcm', $appKey, OPENSSL_RAW_DATA, $iv, $tag);
        return $plain !== false ? $plain : null;
    }

    private static function resolveEncryptionKey(): ?string {
        $rawKey = trim((string)env('APP_KEY', ''));
        if ($rawKey === '') {
            return null;
        }
        return hash('sha256', $rawKey, true);
    }

    private static function base32Encode(string $binary): string {
        $bits = '';
        $len = strlen($binary);
        for ($i = 0; $i < $len; $i++) {
            $bits .= str_pad(decbin(ord($binary[$i])), 8, '0', STR_PAD_LEFT);
        }
        $output = '';
        $chunks = str_split($bits, 5);
        foreach ($chunks as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }
            $output .= self::BASE32_ALPHABET[bindec($chunk)];
        }
        return $output;
    }

    private static function base32Decode(string $base32): string {
        $clean = strtoupper(preg_replace('/[^A-Za-z2-7]/', '', $base32) ?? '');
        if ($clean === '') {
            return '';
        }
        $bits = '';
        $len = strlen($clean);
        for ($i = 0; $i < $len; $i++) {
            $pos = strpos(self::BASE32_ALPHABET, $clean[$i]);
            if ($pos === false) {
                return '';
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }
        $binary = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $binary .= chr(bindec($byte));
            }
        }
        return $binary;
    }
}

