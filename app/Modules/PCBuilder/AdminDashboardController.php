<?php
namespace App\Modules\PCBuilder;

use App\Core\Controller;
use App\Core\Middleware;

class AdminDashboardController extends Controller
{
    private AnalyticsModel $analytics;
    public function __construct()
    {
        Middleware::staff();
        $this->analytics = new AnalyticsModel();
    }

    public function index(): void
    {
        $summary = $this->analytics->summary();
        $this->view('admin.dashboard.index', array_merge($summary, [
            'title' => 'Admin Dashboard — PCMarket',
            'recentOrders' => $this->analytics->recentOrders(7),
            'topProducts' => $this->analytics->popularProducts(5),
        ]));
    }

    public function chartData(): void
    {
        $daily = $this->analytics->dailyPaidRevenue(7);
        $this->json([
            'labels' => $daily['labels'],
            'datasets' => [[
                'label' => 'Doanh thu đã thanh toán (VNĐ)',
                'data' => $daily['data'],
                'borderColor' => '#2454D6',
                'backgroundColor' => 'rgba(36,84,214,.12)',
                'fill' => true,
                'tension' => .32,
            ]],
        ]);
    }
}
