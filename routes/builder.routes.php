<?php
/**
 * [DEV 5 PHỤ TRÁCH] - Module Xây dựng Cấu hình PC & Dashboard Thống kê Admin
 */
use App\Core\Router;

// Client PC Builder
Router::get('/build-pc', 'App\Modules\PCBuilder\PCBuilderController@index');
Router::get('/api/builder/products', 'App\Modules\PCBuilder\PCBuilderController@getComponents');
Router::post('/api/builder/add-to-cart', 'App\Modules\PCBuilder\PCBuilderController@addAllToCart');

// Admin Analytics Dashboard
Router::get('/admin', 'App\Modules\PCBuilder\AdminDashboardController@index');
Router::get('/admin/dashboard', 'App\Modules\PCBuilder\AdminDashboardController@index');
Router::get('/api/admin/chart-data', 'App\Modules\PCBuilder\AdminDashboardController@chartData');

