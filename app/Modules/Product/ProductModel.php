<?php
namespace App\Modules\Product;

use App\Core\Model;

class ProductModel extends Model {
    protected string $table = 'products';

    public function getFeaturedProducts(int $limit = 8): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.is_featured = 1 
                ORDER BY p.id DESC LIMIT {$limit}";
        return $this->query($sql);
    }

    public function getHotSaleGaming(int $limit = 6): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.sku LIKE 'PCM-HOT-%'
                ORDER BY p.id ASC LIMIT {$limit}";
        $items = $this->query($sql);
        if (count($items) < $limit) {
            return $this->getHotGamingPCs($limit);
        }
        return $items;
    }

    public function getHotSaleComponents(int $limit = 6): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.category_id IN (5, 6, 7, 8, 9, 10, 11, 12) AND p.discount_price IS NOT NULL
                ORDER BY p.id DESC LIMIT {$limit}";
        return $this->query($sql);
    }

    public function getGamingPCs(int $limit = 6): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.category_id = 1 AND (p.sku LIKE 'PCM-GM-%' OR p.sku LIKE 'PCM-PC-ROG%')
                ORDER BY p.id ASC LIMIT {$limit}";
        $res = $this->query($sql);
        return $res ?: $this->getHotGamingPCs($limit);
    }

    public function getWorkstationPCs(int $limit = 6): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.category_id IN (2, 15)
                ORDER BY p.id ASC LIMIT {$limit}";
        return $this->query($sql);
    }

    public function getAmdGamingPCs(int $limit = 6): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.category_id = 16
                ORDER BY p.id ASC LIMIT {$limit}";
        return $this->query($sql);
    }

    public function getOfficePCs(int $limit = 6): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.category_id = 3
                ORDER BY p.id ASC LIMIT {$limit}";
        return $this->query($sql);
    }

    public function getMonitors(int $limit = 6): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.category_id = 13
                ORDER BY p.id ASC LIMIT {$limit}";
        return $this->query($sql);
    }

    public function getAiPCs(int $limit = 6): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.category_id = 17
                ORDER BY p.id ASC LIMIT {$limit}";
        return $this->query($sql);
    }

    public function getComponents(int $limit = 6): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.sku LIKE 'PCM-PART-%'
                ORDER BY p.id ASC LIMIT {$limit}";
        $res = $this->query($sql);
        return $res ?: $this->getHotComponents($limit);
    }

    public function getHotGamingPCs(int $limit = 4): array {
        $sql = "SELECT p.*, b.name as brand_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.status = 1 AND (p.category_id = 1 OR p.category_id = 2 OR p.category_id = 16) 
                ORDER BY p.id DESC LIMIT {$limit}";
        return $this->query($sql);
    }

    public function getHotComponents(int $limit = 8): array {
        $sql = "SELECT p.*, b.name as brand_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.status = 1 AND p.category_id >= 4 AND p.category_id <= 14 
                ORDER BY p.id DESC LIMIT {$limit}";
        return $this->query($sql);
    }

    public function findBySlug(string $slug): ?array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.slug = ? AND p.status = 1 LIMIT 1";
        return $this->queryOne($sql, [$slug]);
    }
}
