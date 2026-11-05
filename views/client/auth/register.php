<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>
<div class="page-width auth-layout">
    <aside class="auth-aside"><img src="<?= asset('images/desk-detail.jpg') ?>" alt="Không gian học tập với máy tính" width="600" height="800"><div class="auth-aside-caption"><span class="eyebrow">Một khởi đầu mới</span><h2>Góc máy mới.<br>Những ý tưởng mới.</h2><p>Tạo tài khoản và khám phá chiếc máy phù hợp với bạn.</p></div></aside>
    <section class="auth-form-area" aria-labelledby="auth-title"><span class="eyebrow">Gia nhập PCMarket</span><h1 id="auth-title">Bắt đầu từ đây.</h1><p>Chỉ vài thông tin để tạo tài khoản của bạn.</p>
        <form action="<?= base_url('dang-ky') ?>" method="post" data-validate-form novalidate>
            <?= csrf_field() ?>
            <div class="field"><label for="register-name">Họ và tên</label><input id="register-name" type="text" name="full_name" autocomplete="name" placeholder="Nguyễn Minh Anh" required><span class="field-error" id="register-name-error" hidden></span></div>
            <div class="field-grid"><div class="field"><label for="register-email">Email</label><input id="register-email" type="email" name="email" autocomplete="email" placeholder="ban@example.com" required><span class="field-error" id="register-email-error" hidden></span></div><div class="field"><label for="register-phone">Điện thoại <span>(không bắt buộc)</span></label><input id="register-phone" type="tel" name="phone" autocomplete="tel" placeholder="090 123 4567"><span class="field-error" id="register-phone-error" hidden></span></div></div>
            <div class="field"><label for="register-password">Mật khẩu</label><div class="password-field"><input id="register-password" type="password" name="password" autocomplete="new-password" minlength="6" placeholder="Ít nhất 6 ký tự" required><button type="button" class="icon-button" data-password-toggle aria-controls="register-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button></div><span class="field-error" id="register-password-error" hidden></span></div>
            <div class="field"><label for="register-confirm">Nhập lại mật khẩu</label><div class="password-field"><input id="register-confirm" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Xác nhận mật khẩu" required><button type="button" class="icon-button" data-password-toggle aria-controls="register-confirm" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button></div><span class="field-error" id="register-confirm-error" hidden></span></div>
            <button class="button button-primary form-submit" type="submit">Tạo tài khoản <span aria-hidden="true">↗</span></button>
        </form>
        <div class="auth-switch">Đã có tài khoản? <a href="<?= base_url('dang-nhap') ?>">Đăng nhập</a></div>
    </section>
</div>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
