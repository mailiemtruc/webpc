<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>
<div class="page-width auth-layout">
    <aside class="auth-aside"><img src="<?= asset('images/workspace.jpg') ?>" alt="Góc làm việc sáng với máy tính" width="600" height="800"><div class="auth-aside-caption"><span class="eyebrow">Chào mừng trở lại</span><h2>Tiếp tục những điều<br>bạn đang ấp ủ.</h2><p>Một tài khoản để bắt đầu cùng PCMarket.</p></div></aside>
    <section class="auth-form-area" aria-labelledby="auth-title"><span class="eyebrow">Tài khoản PCMarket</span><h1 id="auth-title">Rất vui được gặp lại bạn.</h1><p>Đăng nhập bằng email và mật khẩu của bạn.</p>
        <form action="<?= base_url('dang-nhap') ?>" method="post" data-validate-form novalidate>
            <?= csrf_field() ?>
            <div class="field"><label for="login-email">Email</label><input id="login-email" type="email" name="email" autocomplete="email" placeholder="ban@example.com" required><span class="field-error" id="login-email-error" hidden></span></div>
            <div class="field"><label for="login-password">Mật khẩu</label><div class="password-field"><input id="login-password" type="password" name="password" autocomplete="current-password" placeholder="Nhập mật khẩu của bạn" required><button type="button" class="icon-button" data-password-toggle aria-controls="login-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button></div><span class="field-error" id="login-password-error" hidden></span></div>
            <button class="button button-primary form-submit" type="submit">Đăng nhập <span aria-hidden="true">↗</span></button>
        </form>
        <div class="auth-switch">Lần đầu ghé PCMarket? <a href="<?= base_url('dang-ky') ?>">Tạo tài khoản</a></div>
        <details class="demo-details"><summary>Tài khoản dùng thử cho đồ án</summary><p>Khách hàng: customer@pcmart.vn / 123456<br>Quản trị: admin@pcmart.vn / admin123</p></details>
    </section>
</div>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
