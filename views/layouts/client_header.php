<?php
$visitor = auth_user();
$catalogCategories = [
    'pc-gaming' => ['PC Gaming', 1, 'gamepad'], 'pc-livestream' => ['PC Livestream', 1, 'video'],
    'pc-do-hoa' => ['PC Đồ họa & Render', 2, 'pen-ruler'], 'pc-van-phong' => ['PC Văn phòng', 3, 'briefcase'],
    'cpu-vi-xu-ly' => ['CPU · Vi xử lý', 5, 'microchip'], 'mainboard-bo-mach-chu' => ['Mainboard', 6, 'server'],
    'vga-card-man-hinh' => ['Card đồ họa', 7, 'display'], 'ram-bo-nho-trong' => ['Bộ nhớ RAM', 8, 'memory'],
    'o-cung-ssd-hdd' => ['SSD & HDD', 9, 'hard-drive'], 'nguon-may-tinh-psu' => ['Nguồn máy tính', 10, 'plug'],
    'vo-case-may-tinh' => ['Case máy tính', 11, 'computer'], 'tan-nhiet-cooling' => ['Tản nhiệt', 12, 'fan'],
    'man-hinh-may-tinh' => ['Màn hình', 13, 'desktop'], 'gaming-gear' => ['Bàn phím & chuột', 14, 'keyboard'],
];
if (preg_match('#/san-pham/?$#', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    $requestedCategory = is_string($_GET['danh-muc'] ?? null) ? $_GET['danh-muc'] : '';
    $title = ($catalogCategories[$requestedCategory][0] ?? ($requestedCategory === 'linh-kien-may-tinh' ? 'Linh kiện máy tính' : 'Tất cả sản phẩm')) . ' — PCMarket';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2454D6">
    <title><?= htmlspecialchars($title ?? 'PCMarket — PC Gaming, Workstation & Linh kiện') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Roboto:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/client.css') ?>?v=6">
    <link rel="stylesheet" href="<?= asset('css/motion.css') ?>?v=1">
    <link rel="stylesheet" href="<?= asset('css/loading.css') ?>?v=1">
    <link rel="stylesheet" href="<?= asset('css/pc-compare.css') ?>?v=1">
    <link rel="stylesheet" href="<?= asset('css/pcm-showcase.css') ?>?v=2">
    <script src="<?= asset('js/ui.js') ?>?v=4" defer></script>
    <script src="<?= asset('js/motion.js') ?>?v=1" defer></script>
    <script src="<?= asset('js/loading.js') ?>?v=1" defer></script>
    <script src="<?= asset('js/pc-compare.js') ?>?v=1" defer></script>
    <script src="<?= asset('js/pcm-homepage.js') ?>?v=1" defer></script>
</head>
<body data-app-base="<?= htmlspecialchars(base_url()) ?>">
<a class="skip-link" href="#main-content">Đến nội dung chính</a>
<div class="store-note"><div class="page-width"><span>PC GAMING / WORKSTATION / LINH KIỆN</span><span class="top-note-right">Chọn cấu hình theo nhu cầu. Xem rõ từng thông số.</span></div></div>
<header class="store-header">
    <div class="page-width header-inner">
        <button class="icon-button mobile-menu-trigger" type="button" data-menu-toggle aria-controls="mobile-menu" aria-expanded="false" aria-label="Mở danh mục"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
        <a class="wordmark" href="<?= base_url() ?>" aria-label="PCMarket — Trang chủ"><span class="brand-symbol" aria-hidden="true"><i class="fa-solid fa-microchip"></i></span><span>PC<span class="brand-accent">MARKET</span><small>MÁY TÍNH & LINH KIỆN</small></span></a>
        <form class="search-form header-search" id="store-search" action="<?= base_url('san-pham') ?>" method="get" role="search" data-search-form novalidate>
            <label class="sr-only" for="site-search">Tìm sản phẩm</label>
            <div class="search-input-wrap"><input id="site-search" type="search" name="q" placeholder="Bạn cần tìm PC, CPU, VGA hay linh kiện nào?" value="<?= htmlspecialchars(is_string($_GET['q'] ?? null) ? $_GET['q'] : '') ?>"><button type="button" class="icon-button" data-search-clear aria-label="Xóa tìm kiếm" hidden>×</button><button class="search-submit" type="submit" aria-label="Tìm kiếm"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button></div>
        </form>
        <div class="header-tools">
            <?php if ($visitor): ?>
            <details class="account-menu"><summary><i class="fa-regular fa-user" aria-hidden="true"></i><span><?= htmlspecialchars($visitor['full_name'] ?? 'Tài khoản') ?></span></summary><div><?php if (is_staff()): ?><a href="<?= base_url('admin') ?>">Quản trị cửa hàng</a><?php endif; ?><a href="<?= base_url('dang-xuat') ?>">Đăng xuất</a></div></details>
            <?php else: ?><a class="account-link" href="<?= base_url('dang-nhap') ?>"><i class="fa-regular fa-user" aria-hidden="true"></i><span>Tài khoản<small>Đăng nhập / Đăng ký</small></span></a><?php endif; ?>
            <a href="<?= base_url('gio-hang') ?>" class="bag-button" aria-label="Giỏ hàng"><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i><span class="bag-label">Giỏ hàng</span><span class="bag-count"><?= count($_SESSION['cart'] ?? []) ?></span></a>
        </div>
    </div>
    <div class="nav-line">
        <div class="page-width pcm-nav-inner">
            <?php require VIEWS_DIR . '/layouts/mega_menu.php'; ?>
            <nav class="pcm-quick-cat-nav" aria-label="Danh mục nổi bật">
                <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>" class="pcm-quick-pill"><i class="fa-solid fa-gamepad" aria-hidden="true"></i> PC GAMING, STREAMING</a>
                <a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>" class="pcm-quick-pill"><i class="fa-solid fa-calculator" aria-hidden="true"></i> PC WORKSTATION</a>
                <a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>" class="pcm-quick-pill"><i class="fa-solid fa-fire" aria-hidden="true"></i> PC AMD GAMING</a>
                <a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>" class="pcm-quick-pill"><i class="fa-solid fa-briefcase" aria-hidden="true"></i> PC VĂN PHÒNG</a>
                <a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>" class="pcm-quick-pill"><i class="fa-solid fa-tv" aria-hidden="true"></i> MÀN HÌNH MÁY TÍNH</a>
                <a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>" class="pcm-quick-pill"><i class="fa-solid fa-brain" aria-hidden="true"></i> PC AI</a>
            </nav>
        </div>
    </div>
    <nav class="mobile-menu page-width" id="mobile-menu" aria-label="Danh mục di động" hidden><a href="<?= base_url('san-pham') ?>">Tất cả sản phẩm <span>→</span></a><?php foreach ($catalogCategories as $slug => $category): ?><a href="<?= base_url('san-pham?danh-muc=' . $slug) ?>"><span><i class="fa-solid fa-<?= $category[2] ?>" aria-hidden="true"></i> <?= $category[0] ?></span><span>→</span></a><?php endforeach; ?></nav>
</header>
<main id="main-content" tabindex="-1">
<?php foreach (['success' => 'Thành công', 'error' => 'Có lỗi xảy ra', 'info' => 'Thông báo'] as $flashType => $flashLabel): ?>
<?php if ($flashMessage = get_flash($flashType)): ?><div class="page-width flash flash-<?= $flashType ?>" role="<?= $flashType === 'error' ? 'alert' : 'status' ?>"><div><strong><?= $flashLabel ?>.</strong> <?= strip_tags($flashMessage, '<a><strong><b><em>') ?></div><button class="icon-button" type="button" data-dismiss aria-label="Đóng thông báo">×</button></div><?php endif; ?>
<?php endforeach; ?>
