<?php
namespace App\Modules\PCBuilder;

use App\Core\Database;
use PDO;

class PCBuilderModel
{
    private PDO $db;
    private const TYPES = [
        'cpu' => 5, 'motherboard' => 6, 'gpu' => 7, 'ram' => 8,
        'storage' => 9, 'psu' => 10, 'case' => 11, 'cooler' => 12,
    ];

    public function __construct() { $this->db = Database::getInstance(); }

    public function components(): array
    {
        $result = [];
        foreach (self::TYPES as $type => $categoryId) {
            $stmt = $this->db->prepare("SELECT id,name,sku,price,discount_price,quantity,thumbnail,specifications,summary
                FROM products WHERE category_id=? AND status=1 ORDER BY quantity DESC, name ASC");
            $stmt->execute([$categoryId]);
            $result[$type] = array_map([$this, 'normalize'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        return $result;
    }

    public function findAvailableProducts(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn($id) => $id > 0)));
        if (!$ids) return [];
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("SELECT id,name,sku,price,discount_price,quantity,thumbnail,specifications,summary FROM products WHERE id IN ({$marks}) AND status=1");
        $stmt->execute($ids);
        $rows = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) $rows[(int)$row['id']] = $this->normalize($row);
        return $rows;
    }

    private function normalize(array $row): array
    {
        $specs = json_decode((string)($row['specifications'] ?? ''), true);
        if (!is_array($specs)) $specs = [];
        $regular = (float)$row['price'];
        $discount = (float)($row['discount_price'] ?? 0);
        $row['current_price'] = ($discount > 0 && $discount < $regular) ? $discount : $regular;
        $row['specs'] = $specs;
        unset($row['specifications']);
        return $row;
    }
}
