<?php
namespace App\Core;

class Auth {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(array $user): void {
        self::init();
        // Không lưu password vào session để bảo mật
        unset($user['password']);
        $_SESSION['user'] = $user;
        session_regenerate_id(true);
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    public static function logout(): void {
        self::init();
        unset($_SESSION['user']);
        session_regenerate_id(true);
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    public static function check(): bool {
        self::init();
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    public static function user(): ?array {
        self::init();
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function role(): ?string {
        $user = self::user();
        return $user['role'] ?? null;
    }

    public static function isAdmin(): bool {
        return self::role() === 'admin';
    }

    public static function isStaff(): bool {
        $role = self::role();
        return $role === 'admin' || $role === 'staff';
    }
}

