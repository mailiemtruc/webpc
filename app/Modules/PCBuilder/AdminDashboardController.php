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
        // Trả về dữ liệu JSON cho biểu đồ Chart.js (demo 7 ngày gần nhất)
        $labels = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'];
        $data = [12500000, 18900000, 35990000, 22400000, 48000000, 65000000, 52000000];

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

