<?php
require VIEWS_DIR . '/layouts/client_header.php';
?>

<div class="pcm-homepage-wrapper">
    <div class="page-width">
        <!-- 1. HERO SHOWCASE: SLIDER CAROUSEL & 3 SUB-BANNERS -->
        <?php
        $slides = !empty($heroBanners) ? $heroBanners : [
            ['title' => 'PC Gaming RTX 50 Series - Hiệu Năng Đỉnh Cao', 'image' => 'assets/images/banners/hero_1.jpg', 'link' => '/san-pham?danh-muc=pc-gaming'],
            ['title' => 'PC AI & Workstation - Render 4K Deep Learning', 'image' => 'assets/images/banners/hero_2.jpg', 'link' => '/san-pham?danh-muc=pc-ai-tri-tue-nhan-tao'],
            ['title' => 'Tự Ráp Cấu Hình PC Builder Chuyên Nghiệp', 'image' => 'assets/images/banners/hero_3.jpg', 'link' => '/build-pc'],
            ['title' => 'Màn Hình 240Hz & Gaming Gear Chính Hãng', 'image' => 'assets/images/banners/hero_4.jpg', 'link' => '/san-pham?danh-muc=man-hinh-may-tinh'],
        ];
        ?>
        <section class="pcm-hero-section" aria-label="Khuyến mại nổi bật">
            <div class="pcm-slider-container" id="pcmHeroSlider">
                <div class="pcm-slider-track">
                    <?php foreach ($slides as $idx => $slide): ?>
                        <?php
                        $rawLink = trim((string)($slide['link'] ?? ''));
                        $slideHref = ($rawLink === '')
                            ? base_url('san-pham')
                            : (preg_match('#^https?://#i', $rawLink) ? $rawLink : base_url(ltrim($rawLink, '/')));
                        $slideImg = product_image_url($slide['image'] ?? '');
                        ?>
                        <div class="pcm-slide <?= $idx === 0 ? 'active' : '' ?>">
                            <a href="<?= htmlspecialchars($slideHref, ENT_QUOTES, 'UTF-8') ?>">
                                <img src="<?= htmlspecialchars($slideImg, ENT_QUOTES, 'UTF-8') ?>?v=3"
                                     alt="<?= htmlspecialchars($slide['title'] ?? 'PCMarket Banner', ENT_QUOTES, 'UTF-8') ?>"
                                     width="1376"
                                     height="768"
                                     <?= $idx === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>
                                     onerror="this.onerror=null; this.src='<?= htmlspecialchars(asset('images/banners/hero_1.jpg'), ENT_QUOTES, 'UTF-8') ?>?v=3';">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Slider Controls -->
                <button type="button" class="pcm-slider-arrow pcm-slider-prev" aria-label="Slide trước">
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </button>
                <button type="button" class="pcm-slider-arrow pcm-slider-next" aria-label="Slide tiếp theo">
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </button>
                <div class="pcm-slider-dots">
                    <?php foreach ($slides as $idx => $slide): ?>
                        <span class="pcm-dot <?= $idx === 0 ? 'active' : '' ?>" data-index="<?= $idx ?>"></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 3 Sub-Banners -->
            <div class="pcm-sub-banners">
                <a class="pcm-sub-banner-item" href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">
                    <img src="<?= asset('images/banners/sub_banner_1.jpg') ?>?v=3" alt="PC Gaming Chiến Mượt 2K/4K" width="1376" height="768">
                </a>
                <a class="pcm-sub-banner-item" href="<?= base_url('san-pham?danh-muc=pc-do-hoa') ?>">
                    <img src="<?= asset('images/banners/sub_banner_2.jpg') ?>?v=3" alt="PC Đồ Họa & Workstation" width="1376" height="768">
                </a>
                <a class="pcm-sub-banner-item" href="<?= base_url('san-pham?danh-muc=linh-kien-may-tinh') ?>">
                    <img src="<?= asset('images/banners/sub_banner_3.jpg') ?>?v=3" alt="Linh Kiện PC Chính Hãng 100%" width="1376" height="768">
                </a>
            </div>
        </section>

        <!-- 2. HOT SALE MỖI NGÀY -->
        <section class="pcm-section pcm-hotsale-section" aria-labelledby="hotsale-title">
            <div class="pcm-hotsale-header">
                <div class="pcm-hotsale-badge">
                    <i class="fa-solid fa-fire-flame-curved" aria-hidden="true"></i>
                    <h2 id="hotsale-title">HOT SALE MỖI NGÀY</h2>
                </div>
                <div class="pcm-hotsale-tabs" role="tablist">
                    <button type="button" 
                            class="pcm-tab-btn active" 
                            data-tab-target="#tab-hotsale-pc" 
                            role="tab" 
                            aria-selected="true">
                        SIÊU KHUYẾN MẠI PC GAMING 2026
                    </button>
                    <button type="button" 
                            class="pcm-tab-btn" 
                            data-tab-target="#tab-hotsale-parts" 
                            role="tab" 
                            aria-selected="false">
                        Top Linh Kiện PC Sale Sốc
                    </button>
                </div>
            </div>

            <div class="pcm-tab-content active" id="tab-hotsale-pc">
                <div class="pcm-product-grid">
                    <?php foreach ($hotSaleGaming as $item): ?>
                        <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pcm-tab-content" id="tab-hotsale-parts">
                <div class="pcm-product-grid">
                    <?php foreach ($hotSaleComponents as $item): ?>
                        <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <a class="pcm-full-more-bar" href="<?= base_url('san-pham') ?>">
                Xem tất cả +
            </a>
        </section>

        <!-- 3. PC GAMING, STREAMING -->
        <section class="pcm-section" aria-labelledby="sec-gaming-title">
            <div class="pcm-section-header pcm-header-red">
                <div class="pcm-section-badge">
                    <h2 id="sec-gaming-title">PC GAMING, STREAMING</h2>
                </div>
                <div class="pcm-section-subtags">
                    <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">MÁY TÍNH CHƠI GAME PCM</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC ĐẸP - LUXURY</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC GAMING PREMIUM</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC Core Ultra</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC STREAMER, YOUTUBER</a>
                </div>
                <a class="pcm-section-more" href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">
                    XEM TẤT CẢ +
                </a>
            </div>
            <div class="pcm-product-grid">
                <?php foreach ($gamingPCs as $item): ?>
                    <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 4. PC WORKSTATION -->
        <section class="pcm-section" aria-labelledby="sec-ws-title">
            <div class="pcm-section-header pcm-header-cyan">
                <div class="pcm-section-badge">
                    <h2 id="sec-ws-title">PC WORKSTATION</h2>
                </div>
                <div class="pcm-section-subtags">
                    <a href="<?= base_url('san-pham?danh-muc=pc-do-hoa') ?>">PC WORKSTATION 2D</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-do-hoa') ?>">PC WORKSTATION 3D</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-do-hoa') ?>">PC Dựng Phim - Edit Video</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-do-hoa') ?>">KHOẢNG GIÁ</a>
                </div>
                <a class="pcm-section-more" href="<?= base_url('san-pham?danh-muc=pc-do-hoa') ?>">
                    XEM TẤT CẢ +
                </a>
            </div>
            <div class="pcm-product-grid">
                <?php foreach ($workstationPCs as $item): ?>
                    <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 5. PC AMD GAMING -->
        <section class="pcm-section" aria-labelledby="sec-amd-title">
            <div class="pcm-section-header pcm-header-red">
                <div class="pcm-section-badge">
                    <h2 id="sec-amd-title">PC AMD GAMING</h2>
                </div>
                <div class="pcm-section-subtags">
                    <a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">AMD Ryzen 5</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">AMD Ryzen 7</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">AMD Ryzen 9 3D V-Cache</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">AM5 Socket</a>
                </div>
                <a class="pcm-section-more" href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">
                    XEM TẤT CẢ +
                </a>
            </div>
            <div class="pcm-product-grid">
                <?php foreach ($amdGamingPCs as $item): ?>
                    <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 6. PC VĂN PHÒNG -->
        <section class="pcm-section" aria-labelledby="sec-office-title">
            <div class="pcm-section-header pcm-header-navy">
                <div class="pcm-section-badge">
                    <h2 id="sec-office-title">PC VĂN PHÒNG</h2>
                </div>
                <div class="pcm-section-subtags">
                    <a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">PC DOANH NGHIỆP - Windows Bản Quyền</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">PC Văn Phòng Theo Nhu Cầu</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">PC Văn Phòng Theo Khoảng Giá</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">PC Trọn Bộ M OFFICE</a>
                </div>
                <a class="pcm-section-more" href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">
                    XEM TẤT CẢ +
                </a>
            </div>
            <div class="pcm-product-grid">
                <?php foreach ($officePCs as $item): ?>
                    <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 7. MÀN HÌNH MÁY TÍNH -->
        <section class="pcm-section" aria-labelledby="sec-monitor-title">
            <div class="pcm-section-header pcm-header-magenta">
                <div class="pcm-section-badge">
                    <h2 id="sec-monitor-title">MÀN HÌNH MÁY TÍNH</h2>
                </div>
                <div class="pcm-section-subtags">
                    <a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">Màn Hình Theo Hãng</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">Kích Thước 24 - 27 Inch</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">Tần Số Quét 100Hz - 300Hz</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">Độ Phân Giải 2K QHD</a>
                </div>
                <a class="pcm-section-more" href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">
                    XEM TẤT CẢ +
                </a>
            </div>
            <div class="pcm-product-grid">
                <?php foreach ($monitors as $item): ?>
                    <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 8. PC AI - TRÍ TUỆ NHÂN TẠO -->
        <section class="pcm-section" aria-labelledby="sec-ai-title">
            <div class="pcm-section-header pcm-header-red">
                <div class="pcm-section-badge">
                    <h2 id="sec-ai-title">PC AI - TRÍ TUỆ NHÂN TẠO</h2>
                </div>
                <div class="pcm-section-subtags">
                    <a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">AI Local Starter</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">AI Local Creator</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">LLaMA / Stable Diffusion Offline</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">VRAM 16GB - 24GB</a>
                </div>
                <a class="pcm-section-more" href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">
                    XEM TẤT CẢ +
                </a>
            </div>
            <div class="pcm-product-grid">
                <?php foreach ($aiPCs as $item): ?>
                    <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 9. LINH KIỆN MÁY TÍNH -->
        <section class="pcm-section" aria-labelledby="sec-parts-title">
            <div class="pcm-section-header pcm-header-red">
                <div class="pcm-section-badge">
                    <h2 id="sec-parts-title">LINH KIỆN MÁY TÍNH</h2>
                </div>
                <div class="pcm-section-subtags">
                    <a href="<?= base_url('san-pham?danh-muc=cpu-vi-xu-ly') ?>">CPU - Bộ vi xử lý</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=mainboard-bo-mach-chu') ?>">Mainboard - Bo mạch chủ</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=vga-card-man-hinh') ?>">VGA - Card màn hình</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=ram-bo-nho-trong') ?>">RAM - Bộ nhớ trong</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=vo-case-may-tinh') ?>">Case - Vỏ máy tính</a>
                </div>
                <a class="pcm-section-more" href="<?= base_url('san-pham?danh-muc=linh-kien-may-tinh') ?>">
                    XEM TẤT CẢ +
                </a>
            </div>
            <div class="pcm-product-grid">
                <?php foreach ($components as $item): ?>
                    <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (!empty($gamingGears)): ?>
        <!-- 10. GAMING GEAR -->
        <section class="pcm-section" aria-labelledby="sec-gear-title">
            <div class="pcm-section-header pcm-header-amber">
                <div class="pcm-section-badge">
                    <h2 id="sec-gear-title">GAMING GEAR</h2>
                </div>
                <div class="pcm-section-subtags">
                    <a href="<?= base_url('san-pham?danh-muc=gaming-gear&q=Bàn+phím') ?>">Bàn Phím Cơ</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=gaming-gear&q=Chuột') ?>">Chuột Chơi Game</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=gaming-gear&q=Tai+nghe') ?>">Tai Nghe Gaming</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=gaming-gear&brand[]=logitech') ?>">Logitech G</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=gaming-gear&brand[]=razer') ?>">Razer</a>
                </div>
                <a class="pcm-section-more" href="<?= base_url('san-pham?danh-muc=gaming-gear') ?>">
                    XEM TẤT CẢ +
                </a>
            </div>
            <div class="pcm-product-grid">
                <?php foreach ($gamingGears as $item): ?>
                    <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php if (!empty($audioVideo)): ?>
        <!-- 11. LOA, MIC, WEBCAM -->
        <section class="pcm-section" aria-labelledby="sec-audio-title">
            <div class="pcm-section-header pcm-header-teal">
                <div class="pcm-section-badge">
                    <h2 id="sec-audio-title">LOA, MIC, WEBCAM</h2>
                </div>
                <div class="pcm-section-subtags">
                    <a href="<?= base_url('san-pham?danh-muc=loa-mic-webcam&q=Loa') ?>">Loa Máy Tính & Kiểm Âm</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=loa-mic-webcam&q=Mic') ?>">Micro Thu Âm & Stream</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=loa-mic-webcam&q=Webcam') ?>">Webcam Full HD / 4K</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('san-pham?danh-muc=loa-mic-webcam&q=Edifier') ?>">Edifier</a>
                </div>
                <a class="pcm-section-more" href="<?= base_url('san-pham?danh-muc=loa-mic-webcam') ?>">
                    XEM TẤT CẢ +
                </a>
            </div>
            <div class="pcm-product-grid">
                <?php foreach ($audioVideo as $item): ?>
                    <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php if (!empty($latestBlogs)): ?>
        <!-- 12. TIN TỨC CÔNG NGHỆ MỚI NHẤT -->
        <section class="pcm-section pcm-blog-section" aria-labelledby="sec-blog-title">
            <div class="pcm-section-header pcm-header-navy">
                <div class="pcm-section-badge">
                    <h2 id="sec-blog-title">TIN TỨC CÔNG NGHỆ</h2>
                </div>
                <div class="pcm-section-subtags">
                    <a href="<?= base_url('tin-tuc') ?>">Tư Vấn Build PC</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('tin-tuc') ?>">Đánh Giá Phần Cứng</a>
                    <span class="pcm-tag-sep">|</span>
                    <a href="<?= base_url('tin-tuc') ?>">Thủ Thuật & Kinh Nghiệm</a>
                </div>
                <a class="pcm-section-more" href="<?= base_url('tin-tuc') ?>">
                    XEM TẤT CẢ +
                </a>
            </div>
            <div class="pcm-blog-grid">
                <?php foreach ($latestBlogs as $post): ?>
                    <article class="pcm-blog-card">
                        <a class="pcm-blog-thumb" href="<?= base_url('tin-tuc/' . rawurlencode($post['slug'])) ?>">
                            <img src="<?= htmlspecialchars(product_image_url($post['thumbnail'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                 loading="lazy">
                        </a>
                        <div class="pcm-blog-body">
                            <div class="pcm-blog-meta">
                                <span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?= date('d/m/Y', strtotime($post['created_at'] ?? 'now')) ?></span>
                                <span><i class="fa-regular fa-eye" aria-hidden="true"></i> <?= number_format((int)($post['view_count'] ?? 0)) ?> lượt xem</span>
                            </div>
                            <h3 class="pcm-blog-title">
                                <a href="<?= base_url('tin-tuc/' . rawurlencode($post['slug'])) ?>">
                                    <?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </h3>
                            <p class="pcm-blog-excerpt"><?= htmlspecialchars($post['summary'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            <a class="pcm-blog-readmore" href="<?= base_url('tin-tuc/' . rawurlencode($post['slug'])) ?>">
                                Đọc chi tiết <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- 13. DỊCH VỤ & CAM KẾT CHÍNH HÃNG PCM -->
        <section class="pcm-assurance-banner" aria-label="Cam kết dịch vụ">
            <div class="pcm-assurance-item">
                <div class="pcm-assurance-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <div class="pcm-assurance-text">
                    <strong>Giao hàng hỏa tốc 2H</strong>
                    <span>Miễn phí nội thành & đóng gói an toàn</span>
                </div>
            </div>
            <div class="pcm-assurance-item">
                <div class="pcm-assurance-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                <div class="pcm-assurance-text">
                    <strong>Ráp máy tại nhà miễn phí</strong>
                    <span>Kỹ thuật viên chuyên nghiệp lắp đặt tận nơi</span>
                </div>
            </div>
            <div class="pcm-assurance-item">
                <div class="pcm-assurance-icon"><i class="fa-solid fa-arrows-rotate"></i></div>
                <div class="pcm-assurance-text">
                    <strong>Đổi mới 30 ngày</strong>
                    <span>Lỗi phần cứng nhà sản xuất đổi ngay</span>
                </div>
            </div>
            <div class="pcm-assurance-item">
                <div class="pcm-assurance-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="pcm-assurance-text">
                    <strong>Bảo hành chính hãng</strong>
                    <span>Hỗ trợ mượn linh kiện thay thế khi bảo hành</span>
                </div>
            </div>
        </section>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
