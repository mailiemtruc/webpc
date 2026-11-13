<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>
<div class="page-width auth-layout">
    <aside class="auth-aside">
        <img src="<?= asset('images/gaming-tower.jpg') ?>" alt="Bộ máy tính gaming minh họa" width="600" height="800">
        <div class="auth-aside-caption">
            <span class="eyebrow">Kích hoạt tài khoản</span>
            <h2>XÁC NHẬN GMAIL.<br>SẴN SÀNG MUA SẮM.</h2>
            <p>Liên kết kích hoạt giúp bảo vệ tài khoản và đảm bảo đúng email chính chủ của bạn.</p>
        </div>
    </aside>
    <section class="auth-form-area" aria-labelledby="auth-title">
        <span class="eyebrow">Xác nhận email</span>
        <h1 id="auth-title">GỬI LẠI LIÊN KẾT XÁC NHẬN</h1>
        <p>Nhập địa chỉ email bạn đã dùng để đăng ký, hệ thống sẽ gửi lại liên kết kích hoạt mới.</p>
        <form action="<?= base_url('gui-lai-xac-nhan') ?>" method="post" data-validate-form novalidate>
            <?= csrf_field() ?>
            <div class="field">
                <label for="verify-email">Email đăng ký</label>
                <input id="verify-email" type="email" name="email" autocomplete="email" value="<?= htmlspecialchars($prefillEmail ?? '') ?>" placeholder="ban@gmail.com" required>
                <span class="field-error" id="verify-email-error" hidden></span>
            </div>
            <button class="button button-primary form-submit" type="submit">Gửi lại email xác nhận <span aria-hidden="true">↗</span></button>
        </form>
        <div class="auth-switch">Đã xác nhận xong? <a href="<?= base_url('dang-nhap') ?>">Quay lại Đăng nhập</a></div>
    </section>
</div>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>

