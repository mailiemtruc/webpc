<?php
namespace App\Modules\PCBuilder;

use App\Core\Database;
use PDO;

/** Truy vấn thống kê thực tế cho Admin Dashboard. */
class AnalyticsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function summary(): array
    {
        $paidRevenue = (float)$this->db->query("SELECT COALESCE(SUM(final_total),0) FROM orders WHERE payment_status='paid' AND status <> 'cancelled'")->fetchColumn();
        $completedRevenue = (float)$this->db->query("SELECT COALESCE(SUM(final_total),0) FROM orders WHERE payment_status='paid' AND status='completed'")->fetchColumn();
        $totalOrders = (int)$this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $totalProducts = (int)$this->db->query("SELECT COUNT(*) FROM products WHERE status=1")->fetchColumn();
        $totalUsers = (int)$this->db->query("SELECT COUNT(*) FROM users WHERE role='customer' AND status=1")->fetchColumn();
        $pendingOrders = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();

        return compact('paidRevenue', 'completedRevenue', 'totalOrders', 'totalProducts', 'totalUsers', 'pendingOrders');
    }

    public function recentOrders(int $limit = 7): array
    {
        $limit = max(1, min(20, $limit));
        return $this->db->query("SELECT * FROM orders ORDER BY created_at DESC, id DESC LIMIT {$limit}")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function popularProducts(int $limit = 5): array
    {
        $limit = max(1, min(10, $limit));
        $sql = "SELECT p.id,p.name,p.slug,p.thumbnail,p.view_count,COALESCE(SUM(CASE WHEN o.status <> 'cancelled' THEN oi.quantity ELSE 0 END),0) AS sold_qty
                FROM products p LEFT JOIN order_items oi ON oi.product_id=p.id
                LEFT JOIN orders o ON o.id=oi.order_id
                WHERE p.status=1 GROUP BY p.id ORDER BY sold_qty DESC,p.view_count DESC LIMIT {$limit}";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dailyPaidRevenue(int $days = 7): array
    {
        $days = max(1, min(31, $days));
        $stmt = $this->db->prepare("SELECT DATE(created_at) AS order_date, COALESCE(SUM(final_total),0) AS revenue
            FROM orders WHERE payment_status='paid' AND status <> 'cancelled'
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY) GROUP BY DATE(created_at)");
        $stmt->execute([$days - 1]);
        $rows = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) $rows[$row['order_date']] = (float)$row['revenue'];
        $labels = $values = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d/m', strtotime($date));
            $values[] = $rows[$date] ?? 0;
        }
        return ['labels' => $labels, 'data' => $values];
    }
}
