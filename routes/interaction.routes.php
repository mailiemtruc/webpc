<?php
/**
 * [DEV 4 PHỤ TRÁCH] - Module Tài khoản, Đánh giá, Tin tức & Banners
 */
use App\Core\Router;

// Client Profile & Reviews & Blogs
Router::get('/tai-khoan', 'App\Modules\Interaction\ProfileController@index');
Router::post('/tai-khoan/update', 'App\Modules\Interaction\ProfileController@update');
Router::get('/tai-khoan/don-hang', 'App\Modules\Interaction\ProfileController@orders');

Router::post('/api/reviews/store', 'App\Modules\Interaction\ReviewController@store');

Router::get('/tin-tuc', 'App\Modules\Interaction\BlogController@index');
Router::get('/tin-tuc/{slug}', 'App\Modules\Interaction\BlogController@detail');

// Admin Users, Reviews, Blogs, Banners
Router::get('/admin/users', 'App\Modules\Interaction\AdminUserController@index');
Router::get('/admin/reviews', 'App\Modules\Interaction\AdminReviewController@index');
Router::get('/admin/blogs', 'App\Modules\Interaction\AdminBlogController@index');
Router::get('/admin/banners', 'App\Modules\Interaction\AdminBannerController@index');

