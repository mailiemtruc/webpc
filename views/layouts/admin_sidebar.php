<aside class="admin-sidebar" id="admin-sidebar" aria-label="Menu quản trị">
    <a class="wordmark wordmark-logo" href="<?= base_url('admin') ?>" aria-label="PCMarket Admin"><img class="brand-logo brand-logo-admin" src="<?= asset('images/logo-pcmarket.png') ?>?v=1" alt="PCMarket" width="136" height="34"></a><span class="workspace-label">Không gian vận hành</span>
    <nav class="admin-navigation">
        <?php
        $requestUri   = $_SERVER['REQUEST_URI'] ?? '';
        $isProducts   = str_contains($requestUri, 'admin/products');
        $isCategories = str_contains($requestUri, 'admin/categories');
        $isBrands     = str_contains($requestUri, 'admin/brands');
        $isOrders     = str_contains($requestUri, 'admin/orders');
        $isCoupons    = str_contains($requestUri, 'admin/coupons');
        $isUsers      = str_contains($requestUri, 'admin/users');
        $isReviews    = str_contains($requestUri, 'admin/reviews');
        $isBlogs      = str_contains($requestUri, 'admin/blogs');
        $isBanners    = str_contains($requestUri, 'admin/banners');
        $isOverview   = !$isProducts && !$isCategories && !$isBrands && !$isOrders && !$isCoupons && !$isUsers && !$isReviews && !$isBlogs && !$isBanners;
        ?>
        <span class="sidebar-caption">Tổng quan</span>
        <a class="sidebar-link <?= $isOverview ? 'is-active' : '' ?>" href="<?= base_url('admin') ?>"><i class="fa-solid fa-table-cells-large" aria-hidden="true"></i>Hoạt động cửa hàng</a>
        
        <span class="sidebar-caption">Quản lý bán hàng</span>
        <?php if (is_admin()): ?>
            <a class="sidebar-link <?= $isProducts ? 'is-active' : '' ?>" href="<?= base_url('admin/products') ?>"><i class="fa-solid fa-box" aria-hidden="true"></i>Sản phẩm & kho hàng</a>
            <a class="sidebar-link <?= $isCategories ? 'is-active' : '' ?>" href="<?= base_url('admin/categories') ?>"><i class="fa-solid fa-folder-tree" aria-hidden="true"></i>Danh mục sản phẩm</a>
            <a class="sidebar-link <?= $isBrands ? 'is-active' : '' ?>" href="<?= base_url('admin/brands') ?>"><i class="fa-solid fa-copyright" aria-hidden="true"></i>Thương hiệu</a>
        <?php endif; ?>
        <a class="sidebar-link <?= $isOrders ? 'is-active' : '' ?>" href="<?= base_url('admin/orders') ?>"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>Đơn hàng</a>
        <a class="sidebar-link <?= $isCoupons ? 'is-active' : '' ?>" href="<?= base_url('admin/coupons') ?>"><i class="fa-solid fa-ticket" aria-hidden="true"></i>Khuyến mãi</a>

        <span class="sidebar-caption">Khách hàng & Đánh giá</span>
        <a class="sidebar-link <?= $isUsers ? 'is-active' : '' ?>" href="<?= base_url('admin/users') ?>"><i class="fa-solid fa-users" aria-hidden="true"></i>Tài khoản khách hàng</a>
        <a class="sidebar-link <?= $isReviews ? 'is-active' : '' ?>" href="<?= base_url('admin/reviews') ?>"><i class="fa-solid fa-star" aria-hidden="true"></i>Duyệt đánh giá</a>

        <span class="sidebar-caption">Nội dung & Truyền thông</span>
        <a class="sidebar-link <?= $isBlogs ? 'is-active' : '' ?>" href="<?= base_url('admin/blogs') ?>"><i class="fa-solid fa-newspaper" aria-hidden="true"></i>Tin tức công nghệ</a>
        <a class="sidebar-link <?= $isBanners ? 'is-active' : '' ?>" href="<?= base_url('admin/banners') ?>"><i class="fa-solid fa-images" aria-hidden="true"></i>Banner & Slider</a>

        <span class="sidebar-caption">Công cụ cửa hàng</span>
        <a class="sidebar-link" href="<?= base_url('build-pc') ?>" target="_blank" rel="noopener"><i class="fa-solid fa-sliders" aria-hidden="true"></i>Cấu hình PC (Builder) ↗</a>
    </nav>
    <div class="sidebar-bottom"><span class="sidebar-project">PCMarket / Quản trị vận hành</span><a class="sidebar-link" href="<?= base_url('tai-khoan/bao-mat') ?>"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i>Bảo mật &amp; 2FA</a><form class="sidebar-logout-form" action="<?= base_url('dang-xuat') ?>" method="post"><?= csrf_field() ?><button class="sidebar-link sidebar-logout-btn" type="submit"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>Đăng xuất</button></form></div>
</aside>
