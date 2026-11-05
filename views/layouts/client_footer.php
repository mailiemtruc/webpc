</main>
<footer class="store-footer">
    <div class="page-width footer-main">
        <div class="footer-intro"><a class="wordmark" href="<?= base_url() ?>">pcmarket<span class="brand-dot">.</span></a><p>Một chiếc máy tính phù hợp.<br>Nhiều điều mới để bắt đầu.</p><span class="footer-caption">PC & linh kiện máy tính</span></div>
        <div><h2>Khám phá</h2><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC Gaming</a><a href="<?= base_url('san-pham?danh-muc=pc-do-hoa') ?>">PC sáng tạo</a><a href="<?= base_url('san-pham?danh-muc=linh-kien-may-tinh') ?>">Linh kiện máy tính</a><a href="<?= base_url('san-pham') ?>">Tất cả sản phẩm</a></div>
        <div><h2>Tài khoản</h2><?php if (is_logged_in()): ?><span><?= htmlspecialchars(auth_user()['full_name']) ?></span><a href="<?= base_url('dang-xuat') ?>">Đăng xuất</a><?php else: ?><a href="<?= base_url('dang-nhap') ?>">Đăng nhập</a><a href="<?= base_url('dang-ky') ?>">Tạo tài khoản</a><?php endif; ?><button class="text-button" type="button" data-feature="cart">Giỏ hàng & đặt hàng</button><button class="text-button" type="button" data-feature="builder">Xây dựng cấu hình</button></div>
        <div class="footer-about"><h2>Về PCMarket</h2><p>Website bán máy tính & linh kiện.<br>Đồ án Lập trình Web — UTH.</p><p>Được thực hiện bởi nhóm sinh viên<br>Đại học Giao thông vận tải TP.HCM.</p></div>
    </div>
    <div class="page-width footer-bottom"><span>© <?= date('Y') ?> PCMarket. Dự án học tập.</span><span>Chọn đúng máy. Làm điều bạn thích.</span><a href="#main-content">Lên đầu trang ↑</a></div>
</footer>
<dialog class="feature-dialog" id="feature-dialog" aria-labelledby="feature-title"><div class="dialog-heading"><span class="eyebrow">PCMarket</span><button type="button" class="icon-button" data-dialog-close aria-label="Đóng">×</button></div><h2 id="feature-title">Tính năng đang hoàn thiện</h2><p id="feature-message"></p><a class="button button-primary" href="<?= base_url('san-pham') ?>">Tiếp tục xem sản phẩm <span aria-hidden="true">↗</span></a></dialog>
</body>
</html>
