<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>
<section class="page-width error-page">
    <div class="error-number" aria-hidden="true">403</div>
    <span class="eyebrow">TRUY CẬP BỊ TỪ CHỐI</span>
    <h1>BẠN KHÔNG CÓ QUYỀN TRUY CẬP</h1>
    <p>Khu vực này yêu cầu tài khoản Quản trị viên hoặc Nhân viên được phân quyền.<br>Vui lòng đăng nhập bằng tài khoản có thẩm quyền hoặc quay về trang chủ.</p>
    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 24px;">
        <a class="button button-primary" href="<?= base_url() ?>">Về trang chủ <span aria-hidden="true">↗</span></a>
        <a class="button button-outline" href="<?= base_url('dang-xuat') ?>">Đăng nhập tài khoản khác</a>
    </div>
</section>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>

