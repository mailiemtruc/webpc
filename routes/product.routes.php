<?php
/**
 * [DEV 2 PHỤ TRÁCH] - Module Sản phẩm, Danh mục, Thương hiệu & Tìm kiếm
 */
use App\Core\Router;

// Client
Router::get('/', 'App\Modules\Product\ProductController@home');
Router::get('/san-pham', 'App\Modules\Product\ProductController@index');
Router::get('/san-pham/{slug}', 'App\Modules\Product\ProductController@detail');
Router::get('/api/search', 'App\Modules\Product\ProductController@liveSearch');

// Admin Management
Router::get('/admin/products', 'App\Modules\Product\AdminProductController@index');
Router::get('/admin/products/create', 'App\Modules\Product\AdminProductController@create');
Router::post('/admin/products/store', 'App\Modules\Product\AdminProductController@store');

Router::get('/admin/categories', 'App\Modules\Product\CategoryController@index');
Router::get('/admin/brands', 'App\Modules\Product\BrandController@index');

