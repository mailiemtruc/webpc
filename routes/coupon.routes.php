<?php
/**
 * [DEV 3 PHỤ TRÁCH] - Module quản lý mã giảm giá
 */
use App\Core\Router;

// Giao diện quản lý mã giảm giá 


// Admin Coupon Routes

Router::get('/admin/coupons', 'App\Modules\Coupon\AdminCouponController@index');
Router::get('/admin/coupons/create', 'App\Modules\Coupon\AdminCouponController@create');
Router::post('/admin/coupons/store', 'App\Modules\Coupon\AdminCouponController@store');
Router::get('/admin/coupons/{id}/edit', 'App\Modules\Coupon\AdminCouponController@edit');
Router::post('/admin/coupons/{id}/update', 'App\Modules\Coupon\AdminCouponController@update');
Router::post('/admin/coupons/{id}/delete', 'App\Modules\Coupon\AdminCouponController@delete');