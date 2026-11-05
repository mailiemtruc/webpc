<?php
$visitor = auth_user();
$catalogCategories = [
    'pc-gaming' => ['PC Gaming', 1], 'pc-do-hoa' => ['PC sáng tạo', 2],
    'pc-van-phong' => ['PC văn phòng', 3], 'cpu-vi-xu-ly' => ['Vi xử lý', 5],
    'vga-card-man-hinh' => ['Card đồ họa', 7], 'ram-bo-nho-trong' => ['Bộ nhớ RAM', 8],
    'o-cung-ssd-hdd' => ['Lưu trữ SSD', 9],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f8f3">
    <title><?= htmlspecialchars($title ?? 'PCMarket — Máy tính cho cuộc sống của bạn') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;450;500;550;600;650;700;750&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/client.css') ?>?v=3">
    <script src="<?= asset('js/ui.js') ?>?v=3" defer></script>
</head>
<body>
<a class="skip-link" href="#main-content">Đến nội dung chính</a>
<div class="store-note"><div class="page-width"><span>Máy tính tốt, bắt đầu từ lựa chọn đúng.</span><a href="<?= base_url('san-pham') ?>">Khám phá cửa hàng <span aria-hidden="true">↗</span></a></div></div>
<header class="store-header">
    <div class="page-width header-inner">
        <a class="wordmark" href="<?= base_url() ?>" aria-label="PCMarket — Trang chủ"><span class="brand-symbol" aria-hidden="true">p<span>c</span></span>pcmarket<span class="brand-dot">.</span></a>
        <nav class="primary-nav" aria-label="Điều hướng chính">
            <a href="<?= base_url('san-pham') ?>">Cửa hàng</a>
            <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">Máy tính bộ</a>
            <a href="<?= base_url('san-pham?danh-muc=linh-kien-may-tinh') ?>">Linh kiện</a>
        </nav>
        <div class="header-tools">
            <button class="icon-button mobile-menu-trigger" type="button" data-menu-toggle aria-controls="mobile-menu" aria-expanded="false" aria-label="Mở danh mục"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
            <button class="icon-button" type="button" data-search-toggle aria-controls="store-search" aria-expanded="false" aria-label="Tìm kiếm sản phẩm"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
            <?php if ($visitor): ?>
                <?php if (is_staff()): ?><a class="account-link" href="<?= base_url('admin') ?>">Quản trị</a><?php endif; ?>
                <a class="account-link" href="<?= base_url('dang-xuat') ?>">Đăng xuất</a>
            <?php else: ?>
                <a class="account-link" href="<?= base_url('dang-nhap') ?>"><i class="fa-regular fa-user" aria-hidden="true"></i><span>Đăng nhập</span></a>
            <?php endif; ?>
            <button type="button" class="bag-button" data-feature="cart"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i><span class="bag-label">Giỏ hàng</span><span class="bag-count"><?= count($_SESSION['cart'] ?? []) ?></span></button>
        </div>
    </div>
    <div class="search-panel" id="store-search" hidden>
        <form class="page-width search-form" action="<?= base_url('san-pham') ?>" method="get" role="search" data-search-form>
            <label for="site-search">Bạn đang tìm gì?</label>
            <div class="search-input-wrap"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><input id="site-search" type="search" name="q" placeholder="Tên máy tính, linh kiện hoặc mã sản phẩm…" value="<?= htmlspecialchars(is_string($_GET['q'] ?? null) ? $_GET['q'] : '') ?>"><button type="button" class="icon-button" data-search-clear aria-label="Xóa tìm kiếm" hidden>×</button><button class="button button-primary" type="submit">Tìm kiếm <span aria-hidden="true">↗</span></button></div>
        </form>
    </div>
    <nav class="mobile-menu page-width" id="mobile-menu" aria-label="Danh mục di động" hidden>
        <a href="<?= base_url('san-pham') ?>">Tất cả sản phẩm <span>↗</span></a>
        <?php foreach ($catalogCategories as $slug => $category): ?><a href="<?= base_url('san-pham?danh-muc=' . $slug) ?>"><?= $category[0] ?><span>↗</span></a><?php endforeach; ?>
    </nav>
</header>
<main id="main-content" tabindex="-1">
<?php foreach (['success' => 'Thành công', 'error' => 'Có lỗi xảy ra', 'info' => 'Thông báo'] as $flashType => $flashLabel): ?>
    <?php if ($flashMessage = get_flash($flashType)): ?><div class="page-width flash flash-<?= $flashType ?>" role="<?= $flashType === 'error' ? 'alert' : 'status' ?>"><div><strong><?= $flashLabel ?>.</strong> <?= htmlspecialchars($flashMessage) ?></div><button class="icon-button" type="button" data-dismiss aria-label="Đóng thông báo">×</button></div><?php endif; ?>
<?php endforeach; ?>
