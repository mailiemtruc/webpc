<?php
namespace App\Modules\Product;

use App\Core\Model;
use App\Core\Database;
use PDO;

class ProductModel extends Model {
    protected string $table = 'products';

    /**
     * Lọc sản phẩm nâng cao đa tiêu chí theo chuẩn siêu thị PCMarket
     * Hỗ trợ: Danh mục cha/con, Khoảng giá, Thương hiệu, CPU, RAM, VGA, Ổ cứng, Từ khóa & Sắp xếp
     */
    public function filterCatalog(array $params, int $perPage = 15): array {
        $db = Database::getInstance();

        $page = max(1, (int)($params['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        $where = ["p.status = 1"];
        $bindings = [];

        // 1. Lọc theo danh mục
        $catSlug = trim($params['danh-muc'] ?? '');
        if ($catSlug !== '') {
            if ($catSlug === 'linh-kien-may-tinh') {
                $where[] = "p.category_id >= 4 AND p.category_id <= 12";
            } else {
                $where[] = "c.slug = ?";
                $bindings[] = $catSlug;
            }
        }

        // 2. Lọc theo danh mục con / nhu cầu phụ
        $subCat = trim($params['sub_cat'] ?? '');
        if ($subCat !== '') {
            $where[] = "(p.name LIKE ? OR p.summary LIKE ? OR p.specifications LIKE ?)";
            $bindings[] = "%{$subCat}%";
            $bindings[] = "%{$subCat}%";
            $bindings[] = "%{$subCat}%";
        }

        // 3. Lọc theo khoảng giá
        $priceRanges = (array)($params['price_range'] ?? []);
        if (!empty($priceRanges)) {
            $priceConditions = [];
            foreach ($priceRanges as $pr) {
                switch ($pr) {
                    case 'duoi-10tr':
                        $priceConditions[] = "COALESCE(p.discount_price, p.price) < 10000000";
                        break;
                    case '10-15tr':
                        $priceConditions[] = "(COALESCE(p.discount_price, p.price) >= 10000000 AND COALESCE(p.discount_price, p.price) < 15000000)";
                        break;
                    case '15-20tr':
                        $priceConditions[] = "(COALESCE(p.discount_price, p.price) >= 15000000 AND COALESCE(p.discount_price, p.price) < 20000000)";
                        break;
                    case '20-25tr':
                        $priceConditions[] = "(COALESCE(p.discount_price, p.price) >= 20000000 AND COALESCE(p.discount_price, p.price) < 25000000)";
                        break;
                    case '25-35tr':
                        $priceConditions[] = "(COALESCE(p.discount_price, p.price) >= 25000000 AND COALESCE(p.discount_price, p.price) < 35000000)";
                        break;
                    case '35-45tr':
                        $priceConditions[] = "(COALESCE(p.discount_price, p.price) >= 35000000 AND COALESCE(p.discount_price, p.price) < 45000000)";
                        break;
                    case '45-60tr':
                        $priceConditions[] = "(COALESCE(p.discount_price, p.price) >= 45000000 AND COALESCE(p.discount_price, p.price) < 60000000)";
                        break;
                    case 'tren-60tr':
                        $priceConditions[] = "COALESCE(p.discount_price, p.price) >= 60000000";
                        break;
                }
            }
            if (!empty($priceConditions)) {
                $where[] = '(' . implode(' OR ', $priceConditions) . ')';
            }
        }

        // 4. Lọc theo thương hiệu (Hãng sản xuất)
        $brands = (array)($params['brand'] ?? []);
        if (!empty($brands)) {
            $brandPlaceholders = implode(',', array_fill(0, count($brands), '?'));
            $where[] = "b.slug IN ({$brandPlaceholders})";
            foreach ($brands as $b) {
                $bindings[] = $b;
            }
        }

        // 5. Lọc theo CPU
        $cpus = (array)($params['cpu'] ?? []);
        if (!empty($cpus)) {
            $cpuConditions = [];
            foreach ($cpus as $cpu) {
                $cleanCpu = str_replace('-', ' ', $cpu);
                $cpuConditions[] = "(p.name LIKE ? OR p.specifications LIKE ?)";
                $bindings[] = "%{$cleanCpu}%";
                $bindings[] = "%{$cleanCpu}%";
            }
            $where[] = '(' . implode(' OR ', $cpuConditions) . ')';
        }

        // 6. Lọc theo RAM
        $rams = (array)($params['ram'] ?? []);
        if (!empty($rams)) {
            $ramConditions = [];
            foreach ($rams as $ram) {
                $ramConditions[] = "(p.name LIKE ? OR p.specifications LIKE ?)";
                $bindings[] = "%{$ram}%";
                $bindings[] = "%{$ram}%";
            }
            $where[] = '(' . implode(' OR ', $ramConditions) . ')';
        }

        // 7. Lọc theo VGA
        $vgas = (array)($params['vga'] ?? []);
        if (!empty($vgas)) {
            $vgaConditions = [];
            foreach ($vgas as $vga) {
                $cleanVga = str_replace('-', ' ', $vga);
                $vgaConditions[] = "(p.name LIKE ? OR p.specifications LIKE ?)";
                $bindings[] = "%{$cleanVga}%";
                $bindings[] = "%{$cleanVga}%";
            }
            $where[] = '(' . implode(' OR ', $vgaConditions) . ')';
        }

        // 8. Lọc theo Ổ cứng
        $storages = (array)($params['storage'] ?? []);
        if (!empty($storages)) {
            $storageConditions = [];
            foreach ($storages as $st) {
                $storageConditions[] = "(p.name LIKE ? OR p.specifications LIKE ?)";
                $bindings[] = "%{$st}%";
                $bindings[] = "%{$st}%";
            }
            $where[] = '(' . implode(' OR ', $storageConditions) . ')';
        }

        // 9. Lọc từ khóa tìm kiếm
        $q = trim($params['q'] ?? '');
        if ($q !== '') {
            $where[] = "(p.name LIKE ? OR p.sku LIKE ? OR p.summary LIKE ?)";
            $bindings[] = "%{$q}%";
            $bindings[] = "%{$q}%";
            $bindings[] = "%{$q}%";
        }

        $whereClause = implode(' AND ', $where);

        // 10. Sắp xếp
        $sort = $params['sort'] ?? 'newest';
        $orderBy = match ($sort) {
            'price-asc' => 'COALESCE(p.discount_price, p.price) ASC, p.id DESC',
            'price-desc' => 'COALESCE(p.discount_price, p.price) DESC, p.id DESC',
            'popular' => 'p.view_count DESC, p.id DESC',
            default => 'p.id DESC'
        };

        // Đếm tổng số kết quả
        $countSql = "SELECT COUNT(p.id) FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     LEFT JOIN brands b ON p.brand_id = b.id 
                     WHERE {$whereClause}";
        $stmtCount = $db->prepare($countSql);
        $stmtCount->execute($bindings);
        $total = (int)$stmtCount->fetchColumn();

        // Lấy danh sách sản phẩm
        $dataSql = "SELECT p.*, b.name as brand_name, b.slug as brand_slug, c.name as category_name, c.slug as category_slug 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    WHERE {$whereClause} 
                    ORDER BY {$orderBy} 
                    LIMIT {$perPage} OFFSET {$offset}";
        $stmtData = $db->prepare($dataSql);
        $stmtData->execute($bindings);
        $products = $stmtData->fetchAll(PDO::FETCH_ASSOC);

        $totalPages = max(1, (int)ceil($total / $perPage));

        return [
            'products' => $products,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'facets' => $this->getFilterFacets($catSlug)
        ];
    }

    /**
     * Tính toán số lượng sản phẩm realtime cho từng mục trong bộ lọc (Facets)
     */
    public function getFilterFacets(string $catSlug = ''): array {
        $db = Database::getInstance();

        $where = "status = 1";
        $binds = [];
        if ($catSlug !== '') {
            if ($catSlug === 'linh-kien-may-tinh') {
                $where .= " AND category_id >= 4 AND category_id <= 12";
            } else {
                $cat = $db->prepare("SELECT id FROM categories WHERE slug = ? LIMIT 1");
                $cat->execute([$catSlug]);
                $catId = $cat->fetchColumn();
                if ($catId) {
                    $where .= " AND category_id = ?";
                    $binds[] = $catId;
                }
            }
        }

        $stmt = $db->prepare("SELECT id, name, category_id, brand_id, COALESCE(discount_price, price) as final_price, specifications FROM products WHERE {$where}");
        $stmt->execute($binds);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 1. Khoảng giá
        $priceRanges = [
            'duoi-10tr' => ['label' => 'Dưới 10 triệu', 'min' => 0, 'max' => 10000000, 'count' => 0],
            '10-15tr'   => ['label' => '10 triệu - 15 triệu', 'min' => 10000000, 'max' => 15000000, 'count' => 0],
            '15-20tr'   => ['label' => '15 triệu - 20 triệu', 'min' => 15000000, 'max' => 20000000, 'count' => 0],
            '20-25tr'   => ['label' => '20 triệu - 25 triệu', 'min' => 20000000, 'max' => 25000000, 'count' => 0],
            '25-35tr'   => ['label' => '25 triệu - 35 triệu', 'min' => 25000000, 'max' => 35000000, 'count' => 0],
            '35-45tr'   => ['label' => '35 triệu - 45 triệu', 'min' => 35000000, 'max' => 45000000, 'count' => 0],
            '45-60tr'   => ['label' => '45 triệu - 60 triệu', 'min' => 45000000, 'max' => 60000000, 'count' => 0],
            'tren-60tr' => ['label' => 'Trên 60 triệu', 'min' => 60000000, 'max' => 9999999999, 'count' => 0],
        ];

        foreach ($all as $item) {
            $fp = (float)$item['final_price'];
            foreach ($priceRanges as $k => &$r) {
                if ($fp >= $r['min'] && $fp < $r['max']) {
                    $r['count']++;
                }
            }
        }

        // 2. Thương hiệu
        $brands = $db->query("SELECT id, name, slug FROM brands WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $brandFacets = [];
        foreach ($brands as $b) {
            $cnt = 0;
            foreach ($all as $item) {
                if ((int)$item['brand_id'] === (int)$b['id']) $cnt++;
            }
            if ($cnt > 0) {
                $brandFacets[$b['slug']] = ['name' => $b['name'], 'count' => $cnt];
            }
        }

        // 3. CPU
        $cpuList = [
            'Ultra 5' => 'Intel Core Ultra 5',
            'Ultra 7' => 'Intel Core Ultra 7',
            'Ultra 9' => 'Intel Core Ultra 9',
            'i3' => 'Intel Core i3',
            'i5' => 'Intel Core i5',
            'i7' => 'Intel Core i7',
            'i9' => 'Intel Core i9',
            'Ryzen 5' => 'AMD Ryzen 5',
            'Ryzen 7' => 'AMD Ryzen 7',
            'Ryzen 9' => 'AMD Ryzen 9',
        ];
        $cpuFacets = [];
        foreach ($cpuList as $key => $label) {
            $cnt = 0;
            foreach ($all as $item) {
                $blob = $item['name'] . ' ' . ($item['specifications'] ?? '');
                if (stripos($blob, $key) !== false) $cnt++;
            }
            if ($cnt > 0) {
                $cpuFacets[$key] = ['label' => $label, 'count' => $cnt];
            }
        }

        // 4. RAM
        $ramList = ['8GB', '16GB', '32GB', '64GB', '128GB'];
        $ramFacets = [];
        foreach ($ramList as $ram) {
            $cnt = 0;
            foreach ($all as $item) {
                $blob = $item['name'] . ' ' . ($item['specifications'] ?? '');
                if (stripos($blob, $ram) !== false) $cnt++;
            }
            if ($cnt > 0) {
                $ramFacets[$ram] = ['label' => $ram, 'count' => $cnt];
            }
        }

        // 5. VGA
        $vgaList = [
            'RTX 3050' => 'GeForce RTX 3050',
            'RTX 3060' => 'GeForce RTX 3060',
            'RTX 4060' => 'GeForce RTX 4060',
            'RTX 4070' => 'GeForce RTX 4070 / Super',
            'RTX 5050' => 'GeForce RTX 5050',
            'RTX 5060' => 'GeForce RTX 5060 / Ti',
            'RTX 5070' => 'GeForce RTX 5070 / Ti',
            'RTX 5080' => 'GeForce RTX 5080',
            'RTX 5090' => 'GeForce RTX 5090',
        ];
        $vgaFacets = [];
        foreach ($vgaList as $key => $label) {
            $cnt = 0;
            foreach ($all as $item) {
                $blob = $item['name'] . ' ' . ($item['specifications'] ?? '');
                if (stripos($blob, $key) !== false) $cnt++;
            }
            if ($cnt > 0) {
                $vgaFacets[$key] = ['label' => $label, 'count' => $cnt];
            }
        }

        return [
            'price_ranges' => $priceRanges,
            'brands' => $brandFacets,
            'cpus' => $cpuFacets,
            'rams' => $ramFacets,
            'vgas' => $vgaFacets,
        ];
    }

    // --- CÁC HÀM TRANG CHỦ & CHI TIẾT ---
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
        return count($items) >= $limit ? $items : $this->getHotGamingPCs($limit);
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
