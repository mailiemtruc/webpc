-- Migration: Thêm google_id cho chức năng Đăng ký / Đăng nhập với Google OAuth 2.0
ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `google_id` VARCHAR(100) NULL UNIQUE AFTER `email`;
