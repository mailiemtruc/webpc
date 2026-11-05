<?php
/**
 * [DEV 3 PHỤ TRÁCH] - Module Giỏ hàng, Mã giảm giá & Đơn hàng
 */
use App\Core\Router;

// Client Cart & Checkout
Router::get('/gio-hang', 'App\Modules\Order\CartController@index');
Router::post('/api/cart/add', 'App\Modules\Order\CartController@add');
Router::post('/api/cart/update', 'App\Modules\Order\CartController@update');
Router::post('/api/cart/remove', 'App\Modules\Order\CartController@remove');

Router::post('/api/coupon/apply', 'App\Modules\Order\CouponController@apply');

Router::get('/thanh-toan', 'App\Modules\Order\CheckoutController@index');
Router::post('/thanh-toan/process', 'App\Modules\Order\CheckoutController@process');
Router::get('/dat-hang-thanh-cong', 'App\Modules\Order\CheckoutController@success');

// Admin Orders & Coupons
Router::get('/admin/orders', 'App\Modules\Order\AdminOrderController@index');
Router::get('/admin/orders/{id}', 'App\Modules\Order\AdminOrderController@detail');
Router::post('/admin/orders/{id}/status', 'App\Modules\Order\AdminOrderController@updateStatus');

Router::get('/admin/coupons', 'App\Modules\Order\AdminCouponController@index');

