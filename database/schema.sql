-- ========================================================
-- CƠ SỞ DỮ LIỆU DỰ ÁN WEBPC - WEBSITE BÁN PC & LINH KIỆN MÁY TÍNH
-- Môn: Lập trình Web - UTH
-- Tham khảo mô hình: PCMarket.vn
-- ========================================================

CREATE DATABASE IF NOT EXISTS `webpc_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `webpc_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. BẢNG NGƯỜI DÙNG (USERS)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(20) NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'staff', 'customer') DEFAULT 'customer',
    `avatar` VARCHAR(255) DEFAULT 'assets/images/default-avatar.png',
    `status` TINYINT(1) DEFAULT 1 COMMENT '1: Hoạt động, 0: Bị khóa',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. BẢNG DANH MỤC SẢN PHẨM (CATEGORIES - Đa cấp)
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `parent_id` INT NULL DEFAULT NULL,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `icon` VARCHAR(50) NULL,
    `display_order` INT DEFAULT 0,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. BẢNG THƯƠNG HIỆU (BRANDS)
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `logo` VARCHAR(255) NULL,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. BẢNG SẢN PHẨM (PRODUCTS)
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `brand_id` INT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `sku` VARCHAR(50) NOT NULL UNIQUE,
    `price` DECIMAL(15, 2) NOT NULL,
    `discount_price` DECIMAL(15, 2) NULL DEFAULT NULL,
    `quantity` INT DEFAULT 10,
    `thumbnail` VARCHAR(255) NOT NULL,
    `summary` TEXT NULL,
    `description` LONGTEXT NULL,
    `specifications` JSON NULL COMMENT 'Lưu thông số kỹ thuật JSON (CPU, RAM, VGA...)',
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_hot` TINYINT(1) DEFAULT 0,
    `status` TINYINT(1) DEFAULT 1,
    `view_count` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. BẢNG NHIỀU ẢNH SẢN PHẨM (PRODUCT_IMAGES - Gallery)
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `display_order` INT DEFAULT 0,
    CONSTRAINT `fk_images_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BẢNG MÃ GIẢM GIÁ (COUPONS)
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL,
    `type` ENUM('percentage', 'fixed') NOT NULL DEFAULT 'fixed' COMMENT 'percentage: giảm %, fixed: giảm tiền mặt',
    `discount_value` DECIMAL(15, 2) NOT NULL,
    `min_order_amount` DECIMAL(15, 2) DEFAULT 0,
    `max_discount` DECIMAL(15, 2) NULL DEFAULT NULL COMMENT 'Áp dụng cho percentage',
    `usage_limit` INT DEFAULT 100,
    `used_count` INT DEFAULT 0,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. BẢNG ĐƠN HÀNG (ORDERS)
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Mã đơn dạng PCM-YYYYMMDD-XXXX',
    `user_id` INT NULL COMMENT 'Null nếu khách mua vãng lai',
    `customer_name` VARCHAR(100) NOT NULL,
    `customer_phone` VARCHAR(20) NOT NULL,
    `customer_email` VARCHAR(150) NULL,
    `shipping_address` TEXT NOT NULL,
    `note` TEXT NULL,
    `subtotal` DECIMAL(15, 2) NOT NULL,
    `coupon_id` INT NULL,
    `coupon_code` VARCHAR(50) NULL,
    `discount_amount` DECIMAL(15, 2) DEFAULT 0,
    `final_total` DECIMAL(15, 2) NOT NULL,
    `payment_method` ENUM('cod', 'banking') DEFAULT 'cod',
    `payment_status` ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    `status` ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_orders_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. BẢNG CHI TIẾT ĐƠN HÀNG (ORDER_ITEMS)
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `product_sku` VARCHAR(50) NULL,
    `product_thumbnail` VARCHAR(255) NULL,
    `price` DECIMAL(15, 2) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `total` DECIMAL(15, 2) NOT NULL,
    CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. BẢNG ĐÁNH GIÁ & BÌNH LUẬN (REVIEWS)
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `rating` TINYINT(1) NOT NULL DEFAULT 5 COMMENT '1 đến 5 sao',
    `comment` TEXT NOT NULL,
    `status` TINYINT(1) DEFAULT 1 COMMENT '1: hiển thị, 0: ẩn/chờ duyệt',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. BẢNG TIN TỨC & BÀI VIẾT (BLOGS)
DROP TABLE IF EXISTS `blogs`;
CREATE TABLE `blogs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `author_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `thumbnail` VARCHAR(255) NOT NULL,
    `summary` TEXT NULL,
    `content` LONGTEXT NOT NULL,
    `view_count` INT DEFAULT 0,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_blogs_author` FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. BẢNG BANNER & SLIDER (BANNERS)
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) NULL,
    `position` VARCHAR(50) DEFAULT 'home_main' COMMENT 'home_main, home_sub1, home_sub2',
    `display_order` INT DEFAULT 0,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

