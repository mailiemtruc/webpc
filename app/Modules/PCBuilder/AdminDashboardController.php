<?php
namespace App\Modules\PCBuilder;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Middleware;

class AdminDashboardController extends Controller {
    public function __construct() {
        // Chỉ cho phép Admin hoặc Staff truy cập
        Middleware::staff();
    }

    public function index(): void {
        $db = Database::getInstance();

        // 1. Thống kê tổng quan KPI
        $revenue = $db->query("SELECT SUM(final_total) as total FROM orders WHERE status != 'cancelled'")->fetchColumn() ?: 0;
        $totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 0;
        $totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn() ?: 0;
        $totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn() ?: 0;

        // 2. 5 đơn hàng mới nhất
        $recentOrders = $db->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();

        // 3. Top 5 sản phẩm bán chạy hoặc xem nhiều
        $topProducts = $db->query("SELECT * FROM products WHERE status = 1 ORDER BY view_count DESC LIMIT 5")->fetchAll();

        $this->view('admin.dashboard.index', [
            'title' => 'Admin Dashboard - Tổng Quan Quản Trị PCMART',
            'revenue' => $revenue,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalUsers' => $totalUsers,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    }

    public function chartData(): void {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT DATE(created_at) AS order_date, COALESCE(SUM(final_total), 0) AS total_revenue
            FROM orders
            WHERE status != 'cancelled'
              AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(created_at)
        ");

        $dailyTotals = [];
        foreach ($stmt->fetchAll() as $row) {
            $dailyTotals[$row['order_date']] = (float)$row['total_revenue'];
        }

        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateKey = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d/m', strtotime($dateKey));
            $data[] = $dailyTotals[$dateKey] ?? 0;
        }

        $this->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Doanh Thu (VNĐ)',
                    'data' => $data,
                    'borderColor' => '#e31837',
                    'backgroundColor' => 'rgba(227, 24, 55, 0.15)',
                    'fill' => true,
                    'tension' => 0.3
                ]
            ]
        ]);
    }
}

