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
Router::get('/admin/products/edit/{id}', 'App\Modules\Product\AdminProductController@edit');
Router::post('/admin/products/update/{id}', 'App\Modules\Product\AdminProductController@update');
Router::post('/admin/products/delete/{id}', 'App\Modules\Product\AdminProductController@delete');

// Admin Categories
Router::get('/admin/categories', 'App\Modules\Product\CategoryController@index');
Router::get('/admin/categories/create', 'App\Modules\Product\CategoryController@create');
Router::post('/admin/categories/store', 'App\Modules\Product\CategoryController@store', ['App\Core\Middleware::csrf']);
Router::get('/admin/categories/edit/{id}', 'App\Modules\Product\CategoryController@edit');
Router::post('/admin/categories/update/{id}', 'App\Modules\Product\CategoryController@update', ['App\Core\Middleware::csrf']);
Router::post('/admin/categories/delete/{id}', 'App\Modules\Product\CategoryController@delete', ['App\Core\Middleware::csrf']);

// Admin Brands
Router::get('/admin/brands', 'App\Modules\Product\BrandController@index');
Router::get('/admin/brands/create', 'App\Modules\Product\BrandController@create');
Router::post('/admin/brands/store', 'App\Modules\Product\BrandController@store', ['App\Core\Middleware::csrf']);
Router::get('/admin/brands/edit/{id}', 'App\Modules\Product\BrandController@edit');
Router::post('/admin/brands/update/{id}', 'App\Modules\Product\BrandController@update', ['App\Core\Middleware::csrf']);
Router::post('/admin/brands/delete/{id}', 'App\Modules\Product\BrandController@delete', ['App\Core\Middleware::csrf']);
