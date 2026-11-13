<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>
<div class="page-width auth-layout">
    <aside class="auth-aside"><img src="<?= asset('images/gaming-tower.jpg') ?>" alt="Bộ máy tính gaming với tản nhiệt và đèn xanh" width="600" height="800"><div class="auth-aside-caption"><span class="eyebrow">Chào mừng trở lại</span><h2>SẴN SÀNG CHO<br>DÀN MÁY TIẾP THEO.</h2><p>Theo dõi và khám phá PC, linh kiện cùng PCMarket.</p></div></aside>
    <section class="auth-form-area" aria-labelledby="auth-title"><span class="eyebrow">Tài khoản PCMarket</span><h1 id="auth-title">ĐĂNG NHẬP TÀI KHOẢN</h1><p>Đăng nhập bằng email và mật khẩu của bạn.</p>
        <form action="<?= base_url('dang-nhap') ?>" method="post" data-validate-form novalidate>
            <?= csrf_field() ?>
            <div class="field"><label for="login-email">Email</label><input id="login-email" type="email" name="email" autocomplete="email" placeholder="ban@example.com" required><span class="field-error" id="login-email-error" hidden></span></div>
            <div class="field"><label for="login-password">Mật khẩu</label><div class="password-field"><input id="login-password" type="password" name="password" autocomplete="current-password" placeholder="Nhập mật khẩu của bạn" required><button type="button" class="icon-button" data-password-toggle aria-controls="login-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button></div><span class="field-error" id="login-password-error" hidden></span></div>
            <div class="forgot-link"><a href="<?= base_url('quen-mat-khau') ?>">Quên mật khẩu?</a></div>
            <button class="button button-primary form-submit" type="submit">Đăng nhập <span aria-hidden="true">↗</span></button>
        </form>
        <div class="auth-oauth-divider"><span>HOẶC</span></div>
        <a href="<?= base_url('auth/google') ?>" class="button-oauth-google" aria-label="Tiếp tục với tài khoản Google">
            <svg class="google-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Đăng nhập với Google</span>
        </a>
        <div class="auth-switch">Lần đầu ghé PCMarket? <a href="<?= base_url('dang-ky') ?>">Tạo tài khoản</a> · <a href="<?= base_url('gui-lai-xac-nhan') ?>">Xác nhận email</a></div>
    </section>
</div>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
