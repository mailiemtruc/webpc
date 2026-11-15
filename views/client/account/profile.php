<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>

<div class="page-width" style="padding: 30px 0 60px;">
    <div style="display: flex; gap: 24px; align-items: flex-start; flex-wrap: wrap;">
        <!-- Sidebar tài khoản -->
        <div style="flex: 1; min-width: 260px; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
            <div style="text-align: center; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; margin-bottom: 16px;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 12px; font-weight: 700;">
                    <i class="fa-regular fa-user"></i>
                </div>
                <h2 style="font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 4px;"><?= htmlspecialchars($profile['full_name'] ?? 'Khách hàng') ?></h2>
                <span style="font-size: 13px; color: #64748b;"><?= htmlspecialchars($profile['email'] ?? '') ?></span>
            </div>
            <nav style="display: flex; flex-direction: column; gap: 6px;">
                <a href="<?= base_url('tai-khoan') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 6px; font-weight: 700; color: #0284c7; background: #f0f9ff; text-decoration: none;">
                    <i class="fa-regular fa-id-card"></i> Thông tin tài khoản
                </a>
                <a href="<?= base_url('tai-khoan/don-hang') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 6px; font-weight: 600; color: #334155; text-decoration: none;">
                    <i class="fa-solid fa-box-archive"></i> Đơn hàng đã mua
                </a>
                <a href="<?= base_url('tai-khoan/bao-mat') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 6px; font-weight: 600; color: #334155; text-decoration: none;">
                    <i class="fa-solid fa-shield-halved"></i> Bảo mật &amp; 2FA
                </a>
            </nav>
        </div>

        <!-- Nội dung chính -->
        <div style="flex: 3; min-width: 320px; display: flex; flex-direction: column; gap: 24px;">
            <!-- Form thông tin cá nhân -->
            <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                <h1 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                    <i class="fa-regular fa-user" style="color: #0284c7;"></i> HỒ SƠ CỦA TÔI
                </h1>
                
                <form action="<?= base_url('tai-khoan/update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Họ và tên (*)</label>
                            <input type="text" name="full_name" required value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Số điện thoại</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" placeholder="0901234567" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Địa chỉ Email</label>
                        <input type="email" disabled value="<?= htmlspecialchars($profile['email'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 6px; font-size: 14px; color: #64748b;">
                        <small style="color: #94a3b8; font-size: 12px;">Email tài khoản không thể chỉnh sửa trực tiếp.</small>
                    </div>

                    <button type="submit" class="button button-primary" style="padding: 10px 24px; background: #0284c7; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                        LƯU THAY ĐỔI
                    </button>
                </form>
            </div>

            <!-- Form Đổi mật khẩu -->
            <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                    <i class="fa-solid fa-key" style="color: #0284c7;"></i> ĐỔI MẬT KHẨU
                </h2>

                <form action="<?= base_url('tai-khoan/doi-mat-khau') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Mật khẩu hiện tại (*)</label>
                        <input type="password" name="current_password" required placeholder="Nhập mật khẩu đang dùng" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Mật khẩu mới (*)</label>
                            <input type="password" name="new_password" required minlength="8" placeholder="Tối thiểu 8 ký tự" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Xác nhận mật khẩu mới (*)</label>
                            <input type="password" name="confirm_password" required minlength="8" placeholder="Nhập lại mật khẩu mới" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                        </div>
                    </div>

                    <button type="submit" class="button button-outline" style="padding: 10px 24px; border: 1px solid #0284c7; color: #0284c7; background: transparent; border-radius: 6px; font-weight: 700; cursor: pointer;">
                        CẬP NHẬT MẬT KHẨU
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
