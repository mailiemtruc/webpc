<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>
<div class="page-width auth-layout">
    <aside class="auth-aside">
        <img src="<?= asset('images/gaming-tower.jpg') ?>" alt="Minh họa bảo mật hai lớp PCMarket" width="600" height="800">
        <div class="auth-aside-caption">
            <span class="eyebrow">Bảo vệ hai lớp (2FA)</span>
            <h2>XÁC THỰC BƯỚC 2.<br>CHỐNG ĐĂNG NHẬP LẠ.</h2>
            <p>Nhập mã 6 chữ số từ ứng dụng Authenticator hoặc dùng 1 mã dự phòng 8 ký tự (kiểu Discord) khi không mang điện thoại.</p>
        </div>
    </aside>
    <section class="auth-form-area" aria-labelledby="auth-title">
        <span class="eyebrow">Xác thực hai yếu tố</span>
        <h1 id="auth-title">NHẬP MÃ BẢO MẬT 2FA</h1>
        <p>Tài khoản: <strong><?= htmlspecialchars($email ?? '') ?></strong></p>

        <div class="tfa-tabs" role="tablist" aria-label="Phương thức xác thực 2FA">
            <button type="button" class="tfa-tab is-active" data-tfa-tab="totp" role="tab" aria-selected="true">
                <i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i> Mã Authenticator (6 số)
            </button>
            <button type="button" class="tfa-tab" data-tfa-tab="backup" role="tab" aria-selected="false">
                <i class="fa-solid fa-key" aria-hidden="true"></i> Mã dự phòng (XXXX-XXXX)
            </button>
        </div>

        <form id="tfa-form-totp" class="tfa-panel" data-tfa-panel="totp" action="<?= base_url('xac-thuc-2fa') ?>" method="post" data-validate-form novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="auth_mode" value="totp">
            <div class="field">
                <label for="tfa-otp-input">Mã xác thực 6 chữ số (Google Authenticator / Authy)</label>
                <input id="tfa-otp-input" class="otp-input" type="text" name="totp_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="000000" required autofocus>
                <span class="field-error" id="tfa-otp-input-error" hidden></span>
            </div>
            <button class="button button-primary form-submit" type="submit">Xác nhận đăng nhập <span aria-hidden="true">↗</span></button>
        </form>

        <form id="tfa-form-backup" class="tfa-panel" data-tfa-panel="backup" action="<?= base_url('xac-thuc-2fa') ?>" method="post" data-validate-form novalidate hidden>
            <?= csrf_field() ?>
            <input type="hidden" name="auth_mode" value="backup">
            <div class="field">
                <label for="tfa-backup-input">Mã dự phòng dùng 1 lần (8 ký tự)</label>
                <input id="tfa-backup-input" class="otp-input" type="text" name="backup_code" maxlength="12" autocomplete="off" placeholder="ABCD-EFGH" required>
                <span class="field-error" id="tfa-backup-input-error" hidden></span>
            </div>
            <button class="button button-primary form-submit" type="submit">Đăng nhập bằng mã dự phòng <span aria-hidden="true">↗</span></button>
        </form>

        <div class="auth-switch tfa-switch-row">
            <form action="<?= base_url('xac-thuc-2fa/huy') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="text-button">← Quay lại đăng nhập bằng tài khoản khác</button>
            </form>
        </div>
    </section>
</div>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>

