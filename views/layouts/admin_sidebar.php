<aside class="admin-sidebar" id="admin-sidebar" aria-label="Menu quản trị">
    <a class="wordmark" href="<?= base_url('admin') ?>">PC<span class="brand-accent">MARKET</span></a><span class="workspace-label">Không gian vận hành</span>
    <nav class="admin-navigation">
        <?php
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $isProducts = str_contains($requestUri, 'admin/products');
        $isOrders   = str_contains($requestUri, 'admin/orders');
        $isCoupons  = str_contains($requestUri, 'admin/coupons');
        $isOverview = !$isProducts && !$isOrders && !$isCoupons;
        ?>
        <span class="sidebar-caption">Tổng quan</span>
        <a class="sidebar-link <?= $isOverview ? 'is-active' : '' ?>" href="<?= base_url('admin') ?>"><i class="fa-solid fa-table-cells-large" aria-hidden="true"></i>Hoạt động cửa hàng</a>
        
        <span class="sidebar-caption">Quản lý bán hàng</span>
        <a class="sidebar-link <?= $isProducts ? 'is-active' : '' ?>" href="<?= base_url('admin/products') ?>"><i class="fa-solid fa-box" aria-hidden="true"></i>Sản phẩm & kho hàng</a>
        <a class="sidebar-link <?= $isOrders ? 'is-active' : '' ?>" href="<?= base_url('admin/orders') ?>"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>Đơn hàng</a>
        <a class="sidebar-link <?= $isCoupons ? 'is-active' : '' ?>" href="<?= base_url('admin/coupons') ?>"><i class="fa-solid fa-ticket" aria-hidden="true"></i>Khuyến mãi</a>

        <span class="sidebar-caption">Các phân hệ đang phát triển</span>
        <?php foreach (['fa-users' => 'Khách hàng', 'fa-newspaper' => 'Nội dung', 'fa-sliders' => 'Cấu hình PC'] as $icon => $label): ?>
            <span class="sidebar-link is-unavailable" title="Phân hệ chưa được kết nối"><i class="fa-solid <?= $icon ?>" aria-hidden="true"></i><?= $label ?><span class="module-dot" aria-label="Đang phát triển"></span></span>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-bottom"><span class="sidebar-project">PCMarket / Đồ án Lập trình Web</span><a class="sidebar-link" href="<?= base_url('dang-xuat') ?>"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>Đăng xuất</a></div>
</aside>
