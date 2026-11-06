<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>
<div class="page-width auth-layout">
    <aside class="auth-aside"><img src="<?= asset('images/gaming-tower.jpg') ?>" alt="Bộ máy tính gaming minh họa" width="600" height="800"><div class="auth-aside-caption"><span class="eyebrow">Bảo mật tài khoản</span><h2>LẤY LẠI QUYỀN<br>TRUY CẬP.</h2><p>Chúng tôi sẽ giúp bạn đặt lại mật khẩu một cách an toàn và nhanh chóng.</p></div></aside>
    <section class="auth-form-area" aria-labelledby="auth-title">
        <span class="eyebrow">Quên mật khẩu</span>
        <h1 id="auth-title">QUÊN MẬT KHẨU</h1>
        <p>Nhập email đăng ký tài khoản của bạn để nhận hướng dẫn khôi phục.</p>
        <form action="<?= base_url('quen-mat-khau') ?>" method="post" data-validate-form novalidate>
            <?= csrf_field() ?>
            <div class="field">
                <label for="reset-email">Email đăng ký</label>
                <input id="reset-email" type="email" name="email" autocomplete="email" placeholder="ban@example.com" required>
                <span class="field-error" id="reset-email-error" hidden></span>
            </div>
            <button class="button button-primary form-submit" type="submit">Gửi yêu cầu <span aria-hidden="true">↗</span></button>
        </form>
        <div class="auth-switch">Đã nhớ lại mật khẩu? <a href="<?= base_url('dang-nhap') ?>">Quay lại Đăng nhập</a></div>
    </section>
</div>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>

