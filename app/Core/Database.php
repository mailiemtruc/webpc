<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $db   = env('DB_NAME', 'webpc_db');
            $user = env('DB_USER', 'root');
            $pass = env('DB_PASS', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die("Lỗi kết nối cơ sở dữ liệu MySQL: " . $e->getMessage() . "<br>Vui lòng kiểm tra file .env và chắc chắn rằng Laragon / MySQL đang chạy.");
            }
        }

        return self::$instance;
    }
}

