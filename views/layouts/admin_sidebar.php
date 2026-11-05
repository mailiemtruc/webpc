<aside class="admin-sidebar" id="admin-sidebar" aria-label="Menu quản trị">
    <a class="wordmark" href="<?= base_url('admin') ?>">pcmarket<span class="brand-dot">.</span></a><span class="workspace-label">Không gian vận hành</span>
    <nav class="admin-navigation"><span class="sidebar-caption">Tổng quan</span><a class="sidebar-link is-active" href="<?= base_url('admin') ?>" aria-current="page"><i class="fa-solid fa-table-cells-large" aria-hidden="true"></i>Hoạt động cửa hàng</a>
    <span class="sidebar-caption">Các phân hệ đang phát triển</span>
    <?php foreach (['fa-box' => 'Sản phẩm & kho hàng', 'fa-bag-shopping' => 'Đơn hàng', 'fa-ticket' => 'Khuyến mãi', 'fa-users' => 'Khách hàng', 'fa-newspaper' => 'Nội dung', 'fa-sliders' => 'Cấu hình PC'] as $icon => $label): ?><span class="sidebar-link is-unavailable" title="Phân hệ chưa được kết nối"><i class="fa-solid <?= $icon ?>" aria-hidden="true"></i><?= $label ?><span class="module-dot" aria-label="Đang phát triển"></span></span><?php endforeach; ?>
    </nav><div class="sidebar-bottom"><span class="sidebar-project">PCMarket / Đồ án Lập trình Web</span><a class="sidebar-link" href="<?= base_url('dang-xuat') ?>"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>Đăng xuất</a></div>
</aside>
