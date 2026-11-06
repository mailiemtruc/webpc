<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>
<div class="page-width auth-layout">
    <aside class="auth-aside"><img src="<?= asset('images/gaming-tower.jpg') ?>" alt="Bộ máy tính gaming minh họa" width="600" height="800"><div class="auth-aside-caption"><span class="eyebrow">Mật khẩu mới</span><h2>BẢO VỆ TÀI KHOẢN.<br>TIẾP TỤC MUA SẮM.</h2><p>Tạo một mật khẩu mạnh để an tâm mua sắm tại PCMarket.</p></div></aside>
    <section class="auth-form-area" aria-labelledby="auth-title">
        <span class="eyebrow">Thiết lập lại</span>
        <h1 id="auth-title">THIẾT LẬP MẬT KHẨU</h1>
        <p>Tài khoản: <strong><?= htmlspecialchars($email ?? '') ?></strong></p>
        <form action="<?= base_url('dat-lai-mat-khau') ?>" method="post" data-validate-form novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
            <div class="field">
                <label for="new-password">Mật khẩu mới</label>
                <div class="password-field">
                    <input id="new-password" type="password" name="password" autocomplete="new-password" minlength="6" placeholder="Tối thiểu 6 ký tự" required>
                    <button type="button" class="icon-button" data-password-toggle aria-controls="new-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                </div>
                <span class="field-error" id="new-password-error" hidden></span>
            </div>
            <div class="field">
                <label for="confirm-password">Xác nhận mật khẩu mới</label>
                <div class="password-field">
                    <input id="confirm-password" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Nhập lại mật khẩu mới" required>
                    <button type="button" class="icon-button" data-password-toggle aria-controls="confirm-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                </div>
                <span class="field-error" id="confirm-password-error" hidden></span>
            </div>
            <button class="button button-primary form-submit" type="submit">Cập nhật mật khẩu <span aria-hidden="true">↗</span></button>
        </form>
        <div class="auth-switch"><a href="<?= base_url('dang-nhap') ?>">Quay lại Đăng nhập</a></div>
    </section>
</div>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>

