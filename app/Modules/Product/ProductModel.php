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

    public function getHotGamingPCs(int $limit = 4): array {
        $sql = "SELECT p.*, b.name as brand_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.status = 1 AND (p.category_id = 1 OR p.category_id = 2) 
                ORDER BY p.id DESC LIMIT {$limit}";
        return $this->query($sql);
    }

    public function getHotComponents(int $limit = 8): array {
        $sql = "SELECT p.*, b.name as brand_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.status = 1 AND p.category_id >= 4 
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

