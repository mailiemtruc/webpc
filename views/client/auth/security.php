<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>
<section class="page-width security-page">
    <header class="security-header">
        <div>
            <span class="eyebrow">Quản lý an toàn tài khoản</span>
            <h1>TRUNG TÂM BẢO MẬT &amp; 2FA</h1>
            <p>Quản lý mật khẩu, theo dõi đăng nhập qua Gmail và bảo vệ tài khoản bằng Authenticator TOTP + mã dự phòng kiểu Discord.</p>
        </div>
        <div class="security-status-pills">
            <span class="status-pill is-verified"><i class="fa-solid fa-envelope-circle-check" aria-hidden="true"></i> Email đã xác nhận</span>
            <?php if ($twoFactorEnabled): ?>
                <span class="status-pill is-enabled"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> 2FA Đang bật</span>
            <?php else: ?>
                <span class="status-pill is-off"><i class="fa-solid fa-shield" aria-hidden="true"></i> 2FA Đang tắt</span>
            <?php endif; ?>
        </div>
    </header>

    <?php if (!empty($freshBackupCodes)): ?>
        <?php $backupTxt = implode("\r\n", array_merge(['PCMarket - Bo Ma Du Phong 2FA (Dung 1 lan)', 'Tai khoan: ' . ($user['email'] ?? ''), 'Ngay tao: ' . date('Y-m-d H:i:s'), str_repeat('-', 40)], $freshBackupCodes)); ?>
        <div class="backup-codes-banner" role="region" aria-labelledby="backup-banner-title">
            <div class="backup-banner-head">
                <div>
                    <span class="eyebrow">LƯU Ý QUAN TRỌNG — CHỈ HIỂN THỊ 1 LẦN</span>
                    <h2 id="backup-banner-title">10 Mã Dự Phòng Đăng Nhập (Kiểu Discord)</h2>
                    <p>Mỗi mã chỉ sử dụng được <strong>1 lần duy nhất</strong> khi bạn mất điện thoại hoặc không mở được ứng dụng Authenticator. Hãy sao chép hoặc tải file <code>.txt</code> về lưu ở nơi an toàn.</p>
                </div>
                <div class="backup-banner-actions">
                    <button type="button" class="button button-outline" data-copy-text="<?= htmlspecialchars(implode("\n", $freshBackupCodes), ENT_QUOTES, 'UTF-8') ?>">
                        <i class="fa-regular fa-copy" aria-hidden="true"></i> Sao chép mã
                    </button>
                    <button type="button" class="button button-primary" data-download-text="<?= htmlspecialchars($backupTxt, ENT_QUOTES, 'UTF-8') ?>" data-download-name="pcmarket-backup-codes.txt">
                        <i class="fa-solid fa-download" aria-hidden="true"></i> Tải file .txt
                    </button>
                </div>
            </div>
            <div class="backup-codes-grid" aria-label="Danh sách 10 mã dự phòng">
                <?php foreach ($freshBackupCodes as $idx => $code): ?>
                    <div class="backup-code-item"><span>#<?= $idx + 1 ?></span><code><?= htmlspecialchars($code) ?></code></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="security-overview-strip">
        <div>
            <span class="strip-label">Tài khoản</span>
            <strong><?= htmlspecialchars($user['full_name'] ?? '') ?></strong>
            <small><?= htmlspecialchars($user['email'] ?? '') ?></small>
        </div>
        <div>
            <span class="strip-label">Vai trò hệ thống</span>
            <strong><?= strtoupper(htmlspecialchars($user['role'] ?? 'customer')) ?></strong>
            <small>Xác nhận email: <?= !empty($user['email_verified_at']) ? htmlspecialchars(date('d/m/Y H:i', strtotime((string)$user['email_verified_at']))) : 'Chưa xác nhận' ?></small>
        </div>
        <div>
            <span class="strip-label">Đăng nhập gần nhất</span>
            <strong><?= !empty($user['last_login_at']) ? htmlspecialchars(date('d/m/Y H:i', strtotime((string)$user['last_login_at']))) : 'Phiên hiện tại' ?></strong>
            <small>IP: <?= htmlspecialchars($user['last_login_ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'N/A')) ?></small>
        </div>
    </div>

    <div class="security-grid">
        <article class="security-card">
            <div class="security-card-head">
                <span class="eyebrow">Xác thực hai yếu tố</span>
                <h2>BẢO MẬT 2FA &amp; MÃ DỰ PHÒNG</h2>
            </div>

            <?php if (!$twoFactorEnabled): ?>
                <p class="security-card-desc">
                    Quét mã QR bằng <strong>Google Authenticator</strong>, <strong>Authy</strong> hoặc <strong>Microsoft Authenticator</strong> để bật 2FA.
                    Sau khi bật, hệ thống sẽ cấp ngay <strong>10 mã dự phòng kiểu Discord</strong> phòng trường hợp mất thiết bị.
                </p>

                <div class="tfa-setup-box">
                    <div class="tfa-qr-wrap">
                        <?= $qrSvg ?>
                    </div>
                    <div class="tfa-secret-info">
                        <h3>1. Quét mã QR hoặc nhập khóa thủ công</h3>
                        <p>Mở ứng dụng Authenticator → chọn thêm tài khoản → quét mã QR bên cạnh hoặc nhập mã khóa Base32:</p>
                        <div class="secret-key-box">
                            <code id="tfa-manual-secret"><?= htmlspecialchars($setupSecret ?? '') ?></code>
                            <button type="button" class="button button-outline" data-copy-text="<?= htmlspecialchars($setupSecret ?? '', ENT_QUOTES, 'UTF-8') ?>">Sao chép khóa</button>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('tai-khoan/bao-mat/2fa/bat') ?>" method="post" data-validate-form novalidate>
                    <?= csrf_field() ?>
                    <h3>2. Xác nhận bật 2FA</h3>
                    <div class="field-grid">
                        <div class="field">
                            <label for="enable-2fa-password">Mật khẩu hiện tại</label>
                            <div class="password-field">
                                <input id="enable-2fa-password" type="password" name="current_password" autocomplete="current-password" placeholder="Nhập mật khẩu để xác nhận" required>
                                <button type="button" class="icon-button" data-password-toggle aria-controls="enable-2fa-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                            </div>
                            <span class="field-error" id="enable-2fa-password-error" hidden></span>
                        </div>
                        <div class="field">
                            <label for="enable-2fa-otp">Mã OTP 6 chữ số từ ứng dụng</label>
                            <input id="enable-2fa-otp" class="otp-input" type="text" name="totp_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="000000" required>
                            <span class="field-error" id="enable-2fa-otp-error" hidden></span>
                        </div>
                    </div>
                    <button class="button button-primary form-submit" type="submit">Kích hoạt 2FA &amp; Nhận 10 mã dự phòng ↗</button>
                </form>
            <?php else: ?>
                <div class="tfa-active-summary">
                    <div>
                        <strong>Tài khoản đã được bảo vệ bằng 2FA</strong>
                        <p>
                            Số mã dự phòng chưa sử dụng còn lại:
                            <strong class="<?= ($unusedBackupCount ?? 0) <= 2 ? 'text-warn' : '' ?>"><?= (int)($unusedBackupCount ?? 0) ?> / 10 mã</strong>
                        </p>
                    </div>
                    <span class="status-pill is-enabled">Đang hoạt động</span>
                </div>

                <div class="tfa-manage-forms">
                    <form class="tfa-subform" action="<?= base_url('tai-khoan/bao-mat/2fa/ma-du-phong') ?>" method="post" data-validate-form novalidate>
                        <?= csrf_field() ?>
                        <h3>Tạo lại 10 mã dự phòng mới</h3>
                        <p>Thao tác này sẽ cấp 10 mã mới (hiển thị 1 lần) và hủy hiệu lực toàn bộ mã dự phòng cũ.</p>
                        <div class="field">
                            <label for="regen-password">Xác nhận mật khẩu hiện tại</label>
                            <div class="password-field">
                                <input id="regen-password" type="password" name="current_password" autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại" required>
                                <button type="button" class="icon-button" data-password-toggle aria-controls="regen-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                            </div>
                            <span class="field-error" id="regen-password-error" hidden></span>
                        </div>
                        <button class="button button-outline" type="submit">Tạo 10 mã dự phòng mới</button>
                    </form>

                    <form class="tfa-subform tfa-danger-box" action="<?= base_url('tai-khoan/bao-mat/2fa/tat') ?>" method="post" data-validate-form novalidate>
                        <?= csrf_field() ?>
                        <h3>Tắt xác thực 2 bước (2FA)</h3>
                        <p>Yêu cầu nhập đúng mật khẩu và 1 mã OTP hiện tại (hoặc 1 mã dự phòng) để tắt bảo vệ 2FA.</p>
                        <div class="field">
                            <label for="disable-password">Mật khẩu hiện tại</label>
                            <div class="password-field">
                                <input id="disable-password" type="password" name="current_password" autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại" required>
                                <button type="button" class="icon-button" data-password-toggle aria-controls="disable-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                            </div>
                            <span class="field-error" id="disable-password-error" hidden></span>
                        </div>
                        <div class="field">
                            <label for="disable-code">Mã OTP (6 số) hoặc Mã dự phòng (XXXX-XXXX)</label>
                            <input id="disable-code" type="text" name="verification_code" autocomplete="off" placeholder="000000 hoặc ABCD-EFGH" required>
                            <span class="field-error" id="disable-code-error" hidden></span>
                        </div>
                        <button class="button button-danger-outline" type="submit">Xác nhận tắt 2FA</button>
                    </form>
                </div>
            <?php endif; ?>
        </article>

        <article class="security-card">
            <div class="security-card-head">
                <span class="eyebrow">Cập nhật định kỳ</span>
                <h2>ĐỔI MẬT KHẨU ĐĂNG NHẬP</h2>
            </div>
            <p class="security-card-desc">
                Sau khi đổi mật khẩu thành công, phiên đăng nhập hiện tại sẽ được xoay vòng token bảo mật và gửi email cảnh báo về hộp thư của bạn.
            </p>
            <form action="<?= base_url('tai-khoan/bao-mat/doi-mat-khau') ?>" method="post" data-validate-form novalidate>
                <?= csrf_field() ?>
                <div class="field">
                    <label for="sec-current-password">Mật khẩu hiện tại</label>
                    <div class="password-field">
                        <input id="sec-current-password" type="password" name="current_password" autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại" required>
                        <button type="button" class="icon-button" data-password-toggle aria-controls="sec-current-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                    </div>
                    <span class="field-error" id="sec-current-password-error" hidden></span>
                </div>
                <div class="field">
                    <label for="sec-new-password">Mật khẩu mới</label>
                    <div class="password-field">
                        <input id="sec-new-password" type="password" name="password" autocomplete="new-password" minlength="6" placeholder="Ít nhất 6 ký tự" required>
                        <button type="button" class="icon-button" data-password-toggle aria-controls="sec-new-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                    </div>
                    <span class="field-error" id="sec-new-password-error" hidden></span>
                </div>
                <div class="field">
                    <label for="sec-confirm-password">Xác nhận mật khẩu mới</label>
                    <div class="password-field">
                        <input id="sec-confirm-password" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Nhập lại mật khẩu mới" required>
                        <button type="button" class="icon-button" data-password-toggle aria-controls="sec-confirm-password" aria-label="Hiện mật khẩu" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                    </div>
                    <span class="field-error" id="sec-confirm-password-error" hidden></span>
                </div>
                <button class="button button-primary form-submit" type="submit">Cập nhật mật khẩu ↗</button>
            </form>
        </article>
    </div>
</section>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>

