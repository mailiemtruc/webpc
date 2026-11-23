<?php
$visitor = auth_user();
$catalogCategories = [
    'pc-gaming' => ['PC Gaming', 1, 'gamepad'], 'pc-livestream' => ['PC Livestream', 1, 'video'],
    'pc-do-hoa' => ['PC Đồ họa & Render', 2, 'pen-ruler'], 'pc-van-phong' => ['PC Văn phòng', 3, 'briefcase'],
    'cpu-vi-xu-ly' => ['CPU · Vi xử lý', 5, 'microchip'], 'mainboard-bo-mach-chu' => ['Mainboard', 6, 'server'],
    'vga-card-man-hinh' => ['Card đồ họa', 7, 'display'], 'ram-bo-nho-trong' => ['Bộ nhớ RAM', 8, 'memory'],
    'o-cung-ssd-hdd' => ['SSD & HDD', 9, 'hard-drive'], 'nguon-may-tinh-psu' => ['Nguồn máy tính', 10, 'plug'],
    'vo-case-may-tinh' => ['Case máy tính', 11, 'computer'], 'tan-nhiet-cooling' => ['Tản nhiệt', 12, 'fan'],
    'man-hinh-may-tinh' => ['Màn hình', 13, 'desktop'], 'gaming-gear' => ['Gaming Gear', 14, 'keyboard'],
    'loa-mic-webcam' => ['Loa, Mic, Webcam', 18, 'headphones'], 'pc-gia-lap-ao-hoa' => ['PC Giả Lập Ảo Hóa', 19, 'server'],
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
    <meta name="csrf-token" content="<?= htmlspecialchars(csrf_token()) ?>">
    <link rel="icon" type="image/png" href="<?= asset('images/favicon.png') ?>?v=1">
    <title><?= htmlspecialchars($title ?? 'PCMarket — PC Gaming, Workstation & Linh kiện') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Roboto:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/client.css') ?>?v=26">
    <link rel="stylesheet" href="<?= asset('css/motion.css') ?>?v=2">
    <link rel="stylesheet" href="<?= asset('css/loading.css') ?>?v=1">
    <link rel="stylesheet" href="<?= asset('css/pc-compare.css') ?>?v=25">
    <link rel="stylesheet" href="<?= asset('css/pcm-showcase.css') ?>?v=5">
    <script src="<?= asset('js/ui.js') ?>?v=8" defer></script>
    <script src="<?= asset('js/motion.js') ?>?v=1" defer></script>
    <script src="<?= asset('js/loading.js') ?>?v=1" defer></script>
    <script src="<?= asset('js/pc-compare.js') ?>?v=2" defer></script>
    <script src="<?= asset('js/pcm-homepage.js') ?>?v=2" defer></script>
</head>
<body data-app-base="<?= htmlspecialchars(base_url()) ?>">
<a class="skip-link" href="#main-content">Đến nội dung chính</a>
<div class="store-note"><div class="page-width"><span>PC GAMING / WORKSTATION / LINH KIỆN</span><span class="top-note-right">Chọn cấu hình theo nhu cầu. Xem rõ từng thông số.</span></div></div>
<header class="store-header">
    <div class="page-width header-inner">
        <button class="icon-button mobile-menu-trigger" type="button" data-menu-toggle aria-controls="mobile-menu" aria-expanded="false" aria-label="Mở danh mục"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
        <a class="wordmark wordmark-logo" href="<?= base_url() ?>" aria-label="PCMarket — Trang chủ"><img class="brand-logo" src="<?= asset('images/logo-pcmarket.png') ?>?v=2" alt="PCMarket" width="227" height="42"></a>
        <form class="search-form header-search" id="store-search" action="<?= base_url('san-pham') ?>" method="get" role="search" data-search-form novalidate>
            <label class="sr-only" for="site-search">Tìm sản phẩm</label>
            <div class="search-input-wrap"><input id="site-search" type="search" name="q" placeholder="Bạn cần tìm PC, CPU, VGA hay linh kiện nào?" value="<?= htmlspecialchars(is_string($_GET['q'] ?? null) ? $_GET['q'] : '') ?>"><button type="button" class="icon-button" data-search-clear aria-label="Xóa tìm kiếm" hidden>×</button><button class="search-submit" type="submit" aria-label="Tìm kiếm"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button></div>
        </form>
        <div class="header-tools">
            <a class="header-tool-pill header-builder-pill" href="<?= base_url('build-pc') ?>">
                <i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i>
                <span>Build PC<small>Tự ráp cấu hình</small></span>
            </a>
            <?php if ($visitor): ?>
            <details class="account-menu">
                <summary>
                    <i class="fa-regular fa-user" aria-hidden="true"></i>
                    <span><?= htmlspecialchars($visitor['full_name'] ?? 'Tài khoản') ?></span>
                </summary>
                <div>
                    <a href="<?= base_url('tai-khoan') ?>"><i class="fa-regular fa-id-card" aria-hidden="true"></i> Thông tin tài khoản</a>
                    <a href="<?= base_url('tai-khoan/don-hang') ?>"><i class="fa-solid fa-box-archive" aria-hidden="true"></i> Đơn hàng của tôi</a>
                    <a href="<?= base_url('tai-khoan/bao-mat') ?>"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Bảo mật &amp; 2FA</a>
                    <?php if (is_staff()): ?>
                        <a href="<?= base_url('admin') ?>"><i class="fa-solid fa-gauge-high" aria-hidden="true"></i> Quản trị cửa hàng</a>
                    <?php endif; ?>
                    <form class="logout-form" action="<?= base_url('dang-xuat') ?>" method="post">
                        <?= csrf_field() ?>
                        <button class="menu-action" type="submit"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Đăng xuất</button>
                    </form>
                </div>
            </details>
            <?php else: ?>
            <a class="account-link" href="<?= base_url('dang-nhap') ?>">
                <i class="fa-regular fa-user" aria-hidden="true"></i>
                <span>Tài khoản<small>Đăng nhập / Đăng ký</small></span>
            </a>
            <?php endif; ?>
            <a href="<?= base_url('gio-hang') ?>" class="bag-button" aria-label="Giỏ hàng"><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i><span class="bag-label">Giỏ hàng</span><span class="bag-count"><?= array_sum(array_map('intval', array_column($_SESSION['cart'] ?? [], 'quantity'))) ?></span></a>
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
                <a href="<?= base_url('build-pc') ?>" class="pcm-quick-pill"><i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i> TỰ RÁP PC</a>
                <a href="<?= base_url('tin-tuc') ?>" class="pcm-quick-pill"><i class="fa-solid fa-newspaper" aria-hidden="true"></i> TIN TỨC</a>
            </nav>
        </div>
    </div>
    <?php
    $mobilePcGroups = [
        'pc-gaming' => ['PC Gaming, Streaming', 'gamepad'], 'pc-workstation' => ['PC Workstation', 'calculator'],
        'pc-amd-gaming' => ['PC AMD Gaming', 'fire'], 'pc-do-hoa' => ['PC Đồ họa & Render', 'pen-ruler'],
        'pc-ai-tri-tue-nhan-tao' => ['PC AI', 'brain'], 'pc-van-phong' => ['PC Văn phòng', 'briefcase'],
        'pc-gia-lap-ao-hoa' => ['PC Giả lập ảo hóa', 'server'],
    ];
    $mobilePartGroups = array_diff_key($catalogCategories, $mobilePcGroups, ['pc-livestream' => 1]);
    ?>
    <div class="mobile-menu-backdrop" data-menu-close hidden></div>
    <nav class="mobile-menu" id="mobile-menu" aria-label="Danh mục di động" hidden>
        <div class="mobile-menu-head">
            <img class="mobile-menu-logo" src="<?= asset('images/logo-pcmarket.png') ?>?v=2" alt="PCMarket" width="227" height="42">
            <button class="mobile-menu-close" type="button" data-menu-close aria-label="Đóng danh mục"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>
        <div class="mobile-menu-body">
            <?php if ($visitor): ?>
            <a class="mobile-menu-account" href="<?= base_url('tai-khoan') ?>">
                <span class="mobile-menu-avatar"><i class="fa-regular fa-user" aria-hidden="true"></i></span>
                <span><strong><?= htmlspecialchars($visitor['full_name'] ?? 'Tài khoản') ?></strong><small>Xem thông tin tài khoản</small></span>
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </a>
            <?php else: ?>
            <div class="mobile-menu-auth">
                <a class="mobile-menu-btn is-primary" href="<?= base_url('dang-nhap') ?>">Đăng nhập</a>
                <a class="mobile-menu-btn" href="<?= base_url('dang-ky') ?>">Đăng ký</a>
            </div>
            <?php endif; ?>

            <div class="mobile-menu-shortcuts">
                <a href="<?= base_url('san-pham') ?>"><i class="fa-solid fa-layer-group" aria-hidden="true"></i><span>Tất cả<br>sản phẩm</span></a>
                <a href="<?= base_url('build-pc') ?>"><i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i><span>Tự ráp<br>cấu hình</span></a>
                <a href="<?= base_url('tin-tuc') ?>"><i class="fa-solid fa-newspaper" aria-hidden="true"></i><span>Tin tức<br>công nghệ</span></a>
            </div>

            <p class="mobile-menu-label">Máy tính bộ</p>
            <div class="mobile-menu-list">
                <?php foreach ($mobilePcGroups as $slug => [$name, $icon]): ?>
                <a href="<?= base_url('san-pham?danh-muc=' . $slug) ?>"><span class="mobile-menu-ico"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i></span><span class="mobile-menu-text"><?= $name ?></span><i class="fa-solid fa-chevron-right mobile-menu-arrow" aria-hidden="true"></i></a>
                <?php endforeach; ?>
            </div>

            <p class="mobile-menu-label">Linh kiện &amp; phụ kiện</p>
            <div class="mobile-menu-list">
                <a href="<?= base_url('san-pham?danh-muc=linh-kien-may-tinh') ?>"><span class="mobile-menu-ico"><i class="fa-solid fa-microchip" aria-hidden="true"></i></span><span class="mobile-menu-text">Tất cả linh kiện</span><i class="fa-solid fa-chevron-right mobile-menu-arrow" aria-hidden="true"></i></a>
                <?php foreach ($mobilePartGroups as $slug => $category): ?>
                <a href="<?= base_url('san-pham?danh-muc=' . $slug) ?>"><span class="mobile-menu-ico"><i class="fa-solid fa-<?= $category[2] ?>" aria-hidden="true"></i></span><span class="mobile-menu-text"><?= $category[0] ?></span><i class="fa-solid fa-chevron-right mobile-menu-arrow" aria-hidden="true"></i></a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
</header>
<main id="main-content" tabindex="-1">
<?php foreach (['success' => 'Thành công', 'error' => 'Có lỗi xảy ra', 'info' => 'Thông báo'] as $flashType => $flashLabel): ?>
<?php if ($flashMessage = get_flash($flashType)): ?><div class="page-width flash flash-<?= $flashType ?>" role="<?= $flashType === 'error' ? 'alert' : 'status' ?>"><div><strong><?= $flashLabel ?>.</strong> <?= strip_tags($flashMessage, '<a><strong><b><em>') ?></div><button class="icon-button" type="button" data-dismiss aria-label="Đóng thông báo">×</button></div><?php endif; ?>
<?php endforeach; ?>
