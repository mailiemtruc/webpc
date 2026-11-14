<?php

/**
 * DEV 4 - Tài khoản, Đánh giá, Tin tức và Banner
 */

use App\Core\Router;

// ==========================================
// CLIENT - TÀI KHOẢN
// ==========================================

Router::get(
    '/tai-khoan',
    'App\Modules\Interaction\ProfileController@index'
);

Router::post(
    '/tai-khoan/update',
    'App\Modules\Interaction\ProfileController@update'
);

Router::get(
    '/tai-khoan/don-hang',
    'App\Modules\Interaction\ProfileController@orders'
);

// ==========================================
// CLIENT - ĐÁNH GIÁ
// ==========================================

Router::post(
    '/api/reviews/store',
    'App\Modules\Interaction\ReviewController@store'
);

// ==========================================
// CLIENT - TIN TỨC
// ==========================================

Router::get(
    '/tin-tuc',
    'App\Modules\Interaction\BlogController@index'
);

Router::get(
    '/tin-tuc/{slug}',
    'App\Modules\Interaction\BlogController@detail'
);

// ==========================================
// ADMIN - QUẢN LÝ TÀI KHOẢN
// ==========================================

Router::get(
    '/admin/users',
    'App\Modules\Interaction\AdminUserController@index'
);

Router::post(
    '/admin/users/{id}/toggle',
    'App\Modules\Interaction\AdminUserController@toggle'
);

// ==========================================
// ADMIN - QUẢN LÝ ĐÁNH GIÁ
// ==========================================

Router::get(
    '/admin/reviews',
    'App\Modules\Interaction\AdminReviewController@index'
);

Router::post(
    '/admin/reviews/{id}/toggle',
    'App\Modules\Interaction\AdminReviewController@toggle'
);

Router::post(
    '/admin/reviews/{id}/delete',
    'App\Modules\Interaction\AdminReviewController@delete'
);

// ==========================================
// ADMIN - QUẢN LÝ TIN TỨC
// ==========================================

Router::get(
    '/admin/blogs',
    'App\Modules\Interaction\AdminBlogController@index'
);

Router::get(
    '/admin/blogs/create',
    'App\Modules\Interaction\AdminBlogController@create'
);

Router::post(
    '/admin/blogs/store',
    'App\Modules\Interaction\AdminBlogController@store'
);

Router::get(
    '/admin/blogs/{id}/edit',
    'App\Modules\Interaction\AdminBlogController@edit'
);

Router::post(
    '/admin/blogs/{id}/update',
    'App\Modules\Interaction\AdminBlogController@update'
);

Router::post(
    '/admin/blogs/{id}/delete',
    'App\Modules\Interaction\AdminBlogController@delete'
);

// ==========================================
// ADMIN - QUẢN LÝ BANNER
// ==========================================

Router::get(
    '/admin/banners',
    'App\Modules\Interaction\AdminBannerController@index'
);

Router::post(
    '/admin/banners/store',
    'App\Modules\Interaction\AdminBannerController@store'
);

Router::post(
    '/admin/banners/{id}/update',
    'App\Modules\Interaction\AdminBannerController@update'
);

Router::post(
    '/admin/banners/{id}/toggle',
    'App\Modules\Interaction\AdminBannerController@toggle'
);

Router::post(
    '/admin/banners/{id}/delete',
    'App\Modules\Interaction\AdminBannerController@delete'
);
