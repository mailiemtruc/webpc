<?php
require VIEWS_DIR . '/layouts/admin_header.php';
require VIEWS_DIR . '/layouts/admin_sidebar.php';
?>

<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">Quản lý tài khoản</h1>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Xem danh sách thành viên và phân quyền hệ thống</p>
        </div>

        <form method="GET" action="<?= base_url('admin/users') ?>" style="display: flex; gap: 8px;">
            <input type="text" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Tìm tên, email..." style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; width: 220px;">
            <button type="submit" class="button button-primary" style="padding: 8px 14px; background: #0284c7; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            <?php if (!empty($q)): ?>
                <a href="<?= base_url('admin/users') ?>" style="padding: 8px 12px; background: #f1f5f9; color: #475569; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center;">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>

    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569; font-size: 12.5px; text-transform: uppercase;">
                    <th style="padding: 12px 16px;">ID</th>
                    <th style="padding: 12px 16px;">Họ và tên</th>
                    <th style="padding: 12px 16px;">Email</th>
                    <th style="padding: 12px 16px;">Số điện thoại</th>
                    <th style="padding: 12px 16px; text-align: center;">Vai trò</th>
                    <th style="padding: 12px 16px; text-align: center;">Trạng thái</th>
                    <th style="padding: 12px 16px;">Ngày tạo</th>
                    <th style="padding: 12px 16px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" style="padding: 36px; text-align: center; color: #64748b;">
                            Không tìm thấy tài khoản nào phù hợp.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px 16px; font-weight: 700; color: #64748b;"><?= (int)$u['id'] ?></td>
                            <td style="padding: 14px 16px; font-weight: 700; color: #0f172a;"><?= htmlspecialchars($u['full_name']) ?></td>
                            <td style="padding: 14px 16px; color: #334155;"><?= htmlspecialchars($u['email']) ?></td>
                            <td style="padding: 14px 16px; color: #64748b;"><?= htmlspecialchars($u['phone'] ?: '—') ?></td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span style="background: #fef2f2; color: #b91c1c; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 800; border: 1px solid #fecaca;">ADMIN</span>
                                <?php elseif ($u['role'] === 'staff'): ?>
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 800; border: 1px solid #bae6fd;">STAFF</span>
                                <?php else: ?>
                                    <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;">USER</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <?php if ((int)$u['status'] === 1): ?>
                                    <span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">Hoạt động</span>
                                <?php else: ?>
                                    <span style="background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px; color: #64748b; font-size: 12.5px;">
                                <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <?php if ($u['role'] !== 'admin' && (int)$u['id'] !== \App\Core\Auth::id()): ?>
                                    <form action="<?= base_url('admin/users/' . (int)$u['id'] . '/toggle') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn <?= (int)$u['status'] === 1 ? 'khóa' : 'mở khóa' ?> tài khoản này?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" style="background: none; border: 1px solid <?= (int)$u['status'] === 1 ? '#ef4444' : '#16a34a' ?>; color: <?= (int)$u['status'] === 1 ? '#ef4444' : '#16a34a' ?>; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                            <?= (int)$u['status'] === 1 ? '<i class="fa-solid fa-lock"></i> Khóa' : '<i class="fa-solid fa-unlock"></i> Mở khóa' ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 12px;">Mặc định</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>

