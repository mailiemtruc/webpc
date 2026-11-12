<?php $admin = auth_user(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2454D6">
    <meta name="csrf-token" content="<?= htmlspecialchars(csrf_token()) ?>">
    <title><?= htmlspecialchars($title ?? 'Quản trị — PCMarket') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Roboto:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/client.css') ?>?v=8"><link rel="stylesheet" href="<?= asset('css/admin.css') ?>?v=5">
    <script src="<?= asset('js/ui.js') ?>?v=6" defer></script>
    <link rel="stylesheet" href="<?= asset('css/motion.css') ?>?v=1">
    <script src="<?= asset('js/motion.js') ?>?v=1" defer></script>
</head>
<body class="admin-body">
<a class="skip-link" href="#main-content">Đến nội dung chính</a>
<?php require VIEWS_DIR . '/layouts/admin_sidebar.php'; ?>
<button class="admin-backdrop" type="button" data-admin-backdrop aria-label="Đóng menu quản trị" hidden></button>
<div class="admin-main">
    <header class="admin-topbar"><div class="admin-topbar-left"><button class="icon-button admin-menu-trigger" type="button" data-admin-menu aria-controls="admin-sidebar" aria-expanded="false" aria-label="Mở menu quản trị"><i class="fa-solid fa-bars" aria-hidden="true"></i></button><span>Không gian quản trị <span class="admin-divider">/</span> <strong>Tổng quan</strong></span></div><div class="admin-topbar-right"><a href="<?= base_url() ?>">Xem cửa hàng ↗</a><span class="admin-avatar" aria-hidden="true"><?= htmlspecialchars(mb_substr($admin['full_name'] ?? 'A', 0, 1, 'UTF-8')) ?></span><span class="admin-user-name"><?= htmlspecialchars($admin['full_name'] ?? 'Quản trị viên') ?></span></div></header>
    <main class="admin-content" id="main-content" tabindex="-1">
    <?php foreach (['success', 'error', 'info'] as $type): ?><?php if ($message = get_flash($type)): ?><div class="flash flash-<?= $type ?>" role="<?= $type === 'error' ? 'alert' : 'status' ?>"><span><?= htmlspecialchars($message) ?></span><button type="button" class="icon-button" data-dismiss aria-label="Đóng thông báo">×</button></div><?php endif; ?><?php endforeach; ?>
