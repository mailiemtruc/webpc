<?php
namespace App\Core;

class Request {
    public static function method(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function isPost(): bool {
        return self::method() === 'POST';
    }

    public static function isGet(): bool {
        return self::method() === 'GET';
    }

    public static function all(): array {
        $data = array_merge($_GET, $_POST);
        return self::sanitize($data);
    }

    public static function get(string $key, $default = null) {
        $val = $_GET[$key] ?? $default;
        return is_string($val) ? trim(htmlspecialchars($val, ENT_QUOTES, 'UTF-8')) : $val;
    }

    public static function post(string $key, $default = null) {
        $val = $_POST[$key] ?? $default;
        if (!is_string($val)) {
            return $val;
        }
        // Các trường mật khẩu bí mật không bị htmlspecialchars biến đổi ký tự
        if (in_array($key, ['password', 'password_confirmation', 'old_password', 'current_password', 'new_password'], true)) {
            return $val;
        }
        return trim(htmlspecialchars($val, ENT_QUOTES, 'UTF-8'));
    }

    public static function raw(string $key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function file(string $key) {
        return $_FILES[$key] ?? null;
    }

    public static function validateCsrf(): bool {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = $_SESSION['_csrf_token'] ?? '';
        return !empty($token) && hash_equals($sessionToken, $token);
    }

    private static function sanitize(array $data): array {
        $clean = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $clean[$key] = self::sanitize($value);
            } elseif (is_string($value)) {
                $clean[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            } else {
                $clean[$key] = $value;
            }
        }
        return $clean;
    }
}

