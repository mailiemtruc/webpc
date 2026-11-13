-- Migration: Bổ sung Xác nhận Email, Lịch sử đăng nhập gần nhất & Xác thực 2 bước (TOTP + Mã dự phòng kiểu Discord)
USE `webpc_db`;

ALTER TABLE `users`
    ADD COLUMN `email_verified_at` DATETIME NULL AFTER `status`,
    ADD COLUMN `verification_token` VARCHAR(100) NULL AFTER `email_verified_at`,
    ADD COLUMN `verification_expires` DATETIME NULL AFTER `verification_token`,
    ADD COLUMN `two_factor_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `reset_expires`,
    ADD COLUMN `two_factor_secret` VARCHAR(255) NULL AFTER `two_factor_enabled`,
    ADD COLUMN `two_factor_confirmed_at` DATETIME NULL AFTER `two_factor_secret`,
    ADD COLUMN `last_totp_slice` INT UNSIGNED NULL AFTER `two_factor_confirmed_at`,
    ADD COLUMN `last_login_at` DATETIME NULL AFTER `last_totp_slice`,
    ADD COLUMN `last_login_ip` VARCHAR(45) NULL AFTER `last_login_at`;

CREATE TABLE IF NOT EXISTS `user_backup_codes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `code_hash` VARCHAR(255) NOT NULL,
    `used_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_backup_codes_user_unused` (`user_id`, `used_at`),
    CONSTRAINT `fk_backup_codes_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đánh dấu đã xác thực email cho các tài khoản seeded/hiện hữu trước migration
UPDATE `users`
SET `email_verified_at` = COALESCE(`email_verified_at`, NOW())
WHERE `email_verified_at` IS NULL;

