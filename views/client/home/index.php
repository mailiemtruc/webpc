<?php
require VIEWS_DIR . '/layouts/client_header.php';
?>

<div class="pcm-homepage-wrapper">
    <div class="page-width">
        <!-- 1. HERO SHOWCASE: SLIDER CAROUSEL & 3 SUB-BANNERS -->
        <section class="pcm-hero-section" aria-label="Khuyến mại nổi bật">
            <div class="pcm-slider-container" id="pcmHeroSlider">
                <div class="pcm-slider-track">
                    <div class="pcm-slide active">
                        <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">
                            <img src="<?= asset('images/banners/hero_delta_force.jpg') ?>" 
                                 alt="Top Cấu Hình PC Chiến Delta Force - Trải nghiệm đỉnh cao" 
                                 width="1003" 
                                 height="350" 
                                 fetchpriority="high">
                        </a>
                    </div>
                    <div class="pcm-slide">
                        <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">
                            <img src="<?= asset('images/banners/hero_slide_2.jpg') ?>" 
                                 alt="PC Gaming RTX 40 & 50 Series - Thống trị mọi tựa game" 
                                 width="1003" 
                                 height="350">
                        </a>
                    </div>
                    <div class="pcm-slide">
                        <a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">
                            <img src="<?= asset('images/banners/hero_slide_3.jpg') ?>" 
                                 alt="PC AI & Deep Learning Studio 2026" 
                                 width="1003" 
                                 height="350">
                        </a>
                    </div>
                </div>

                <!-- Slider Controls -->
                <button type="button" class="pcm-slider-arrow pcm-slider-prev" aria-label="Slide trước">
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </button>
                <button type="button" class="pcm-slider-arrow pcm-slider-next" aria-label="Slide tiếp theo">
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </button>
                <div class="pcm-slider-dots">
                    <span class="pcm-dot active" data-index="0"></span>
                    <span class="pcm-dot" data-index="1"></span>
                    <span class="pcm-dot" data-index="2"></span>
                </div>
            </div>

            <!-- 3 Sub-Banners -->
            <div class="pcm-sub-banners">
                <a class="pcm-sub-banner-item" href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">
                    <img src="<?= asset('images/banners/sub_banner_1.jpg') ?>" alt="Chiến Delta Force" width="330" height="185">
                </a>
                <a class="pcm-sub-banner-item" href="<?= base_url('san-pham?danh-muc=pc-do-hoa') ?>">
                    <img src="<?= asset('images/banners/sub_banner_2.jpg') ?>" alt="PC Gaming Premium Luxury" width="330" height="185">
                </a>
                <a class="pcm-sub-banner-item" href="<?= base_url('san-pham') ?>">
                    <img src="<?= asset('images/banners/sub_banner_3.jpg') ?>" alt="PCM Ráp máy tại nhà" width="330" height="185">
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

        <!-- 10. DỊCH VỤ & CAM KẾT CHÍNH HÃNG PCM -->
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
