<?php
/**
 * Mega Menu Đa Cấp Chuẩn PCMarket.vn (Ảnh 4)
 * Cột trái: 13 danh mục chính có icon màu sắc
 * Cột phải: 4 cột danh mục con, phân loại nhu cầu & tầm giá
 */
?>
<div class="pcm-mega-menu-container">
    <div class="pcm-mega-menu-trigger">
        <i class="fa-solid fa-bars" aria-hidden="true"></i>
        <span>DANH MỤC SẢN PHẨM</span>
        <i class="fa-solid fa-chevron-down pcm-caret" aria-hidden="true"></i>
    </div>

    <div class="pcm-mega-menu-dropdown" id="pcmMegaDropdown">
        <!-- CỘT TRÁI: DANH SÁCH DANH MỤC LỚN -->
        <ul class="pcm-mega-cats-list">
            <li class="pcm-mega-cat-item is-active" data-target="#panel-gaming">
                <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">
                    <span class="pcm-mega-icon"><i class="fa-solid fa-gamepad" style="color: #e51a24;"></i></span>
                    <span class="pcm-mega-name">PC GAMING, STREAMING</span>
                    <i class="fa-solid fa-chevron-right pcm-mega-arrow"></i>
                </a>
            </li>
            <li class="pcm-mega-cat-item" data-target="#panel-ws">
                <a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">
                    <span class="pcm-mega-icon"><i class="fa-solid fa-calculator" style="color: #0284c7;"></i></span>
                    <span class="pcm-mega-name">PC WORKSTATION</span>
                    <i class="fa-solid fa-chevron-right pcm-mega-arrow"></i>
                </a>
            </li>
            <li class="pcm-mega-cat-item" data-target="#panel-amd">
                <a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">
                    <span class="pcm-mega-icon"><i class="fa-solid fa-fire" style="color: #ea580c;"></i></span>
                    <span class="pcm-mega-name">PC AMD GAMING</span>
                    <i class="fa-solid fa-chevron-right pcm-mega-arrow"></i>
                </a>
            </li>
            <li class="pcm-mega-cat-item" data-target="#panel-office">
                <a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">
                    <span class="pcm-mega-icon"><i class="fa-solid fa-briefcase" style="color: #1e3a8a;"></i></span>
                    <span class="pcm-mega-name">PC VĂN PHÒNG</span>
                    <i class="fa-solid fa-chevron-right pcm-mega-arrow"></i>
                </a>
            </li>
            <li class="pcm-mega-cat-item" data-target="#panel-monitor">
                <a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">
                    <span class="pcm-mega-icon"><i class="fa-solid fa-tv" style="color: #db2777;"></i></span>
                    <span class="pcm-mega-name">MÀN HÌNH MÁY TÍNH</span>
                    <i class="fa-solid fa-chevron-right pcm-mega-arrow"></i>
                </a>
            </li>
            <li class="pcm-mega-cat-item" data-target="#panel-ai">
                <a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">
                    <span class="pcm-mega-icon"><i class="fa-solid fa-brain" style="color: #9333ea;"></i></span>
                    <span class="pcm-mega-name">PC AI - TRÍ TUỆ NHÂN TẠO</span>
                    <i class="fa-solid fa-chevron-right pcm-mega-arrow"></i>
                </a>
            </li>
            <li class="pcm-mega-cat-item" data-target="#panel-parts">
                <a href="<?= base_url('san-pham?danh-muc=linh-kien-may-tinh') ?>">
                    <span class="pcm-mega-icon"><i class="fa-solid fa-microchip" style="color: #059669;"></i></span>
                    <span class="pcm-mega-name">LINH KIỆN MÁY TÍNH</span>
                    <i class="fa-solid fa-chevron-right pcm-mega-arrow"></i>
                </a>
            </li>
            <li class="pcm-mega-cat-item" data-target="#panel-gear">
                <a href="<?= base_url('san-pham?danh-muc=gaming-gear') ?>">
                    <span class="pcm-mega-icon"><i class="fa-solid fa-keyboard" style="color: #f59e0b;"></i></span>
                    <span class="pcm-mega-name">GAMING GEAR (PHÍM/CHUỘT)</span>
                    <i class="fa-solid fa-chevron-right pcm-mega-arrow"></i>
                </a>
            </li>
            <li class="pcm-mega-cat-item" data-target="#panel-audio">
                <a href="<?= base_url('san-pham?danh-muc=linh-kien-may-tinh') ?>">
                    <span class="pcm-mega-icon"><i class="fa-solid fa-headphones" style="color: #0284c7;"></i></span>
                    <span class="pcm-mega-name">LOA, MIC, WEBCAM</span>
                    <i class="fa-solid fa-chevron-right pcm-mega-arrow"></i>
                </a>
            </li>
            <li class="pcm-mega-cat-item" data-target="#panel-gaming">
                <a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">
                    <span class="pcm-mega-icon"><i class="fa-solid fa-server" style="color: #6366f1;"></i></span>
                    <span class="pcm-mega-name">PC GIẢ LẬP ẢO HÓA</span>
                    <i class="fa-solid fa-chevron-right pcm-mega-arrow"></i>
                </a>
            </li>
        </ul>

        <!-- CỘT PHẢI: BẢNG NỘI DUNG MEGA EXPANDED THEO TỪNG DANH MỤC -->
        <div class="pcm-mega-content-area">
            <!-- 1. PANEL PC GAMING -->
            <div class="pcm-mega-subpanel is-active" id="panel-gaming">
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">MÁY TÍNH CHƠI GAME PCM</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&price_range[]=duoi-10tr') ?>">PC GAMING GIÁ RẺ</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&price_range[]=15-20tr') ?>">PC GAMING TRUNG CẤP</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&price_range[]=35-45tr') ?>">PC GAMING CAO CẤP</a></li>
                    </ul>
                    <h4 class="pcm-mega-group-title" style="margin-top: 18px;"><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC STREAMER, YOUTUBER</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&cpu[]=i7') ?>">Livestream OBS 2K/4K</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&ram[]=32GB') ?>">Dàn Máy Kèm Capture Card</a></li>
                    </ul>
                </div>

                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC ĐẸP - LUXURY</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC Bể Cá Kính Vô Cực</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC Gaming Tone Trắng</a></li>
                    </ul>
                    <h4 class="pcm-mega-group-title" style="margin-top: 18px;"><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC GAMING DDR5</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&ram[]=16GB') ?>">RAM 16GB DDR5 5600MHz</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&ram[]=32GB') ?>">RAM 32GB DDR5 6000MHz</a></li>
                    </ul>
                </div>

                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC GAMING PREMIUM</a></h4>
                    <h4 class="pcm-mega-group-title" style="margin-top: 14px; color: #64748b; font-size: 12.5px;">Theo Khoảng Giá</h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&price_range[]=duoi-10tr') ?>">Dưới 10 Triệu</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&price_range[]=10-15tr') ?>">10 Triệu - 15 Triệu</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&price_range[]=15-20tr') ?>">15 Triệu - 20 Triệu</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&price_range[]=20-25tr') ?>">20 Triệu - 25 Triệu</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&price_range[]=25-35tr') ?>">25 Triệu - 35 Triệu</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&price_range[]=35-45tr') ?>">35 Triệu - 45 Triệu</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&price_range[]=tren-60tr') ?>">Trên 50 Triệu</a></li>
                    </ul>
                </div>

                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">PC Core Ultra</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&cpu[]=Ultra-5') ?>">PC CORE ULTRA 5</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&cpu[]=Ultra-7') ?>">PC CORE ULTRA 7</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming&cpu[]=Ultra-9') ?>">PC CORE ULTRA 9</a></li>
                    </ul>
                    <h4 class="pcm-mega-group-title" style="margin-top: 18px;"><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">Full Bộ PC Kèm Màn Hình</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">Combo PC + Màn 100Hz</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-gaming') ?>">Combo PC + Màn 240Hz Gaming</a></li>
                    </ul>
                </div>
            </div>

            <!-- 2. PANEL PC WORKSTATION -->
            <div class="pcm-mega-subpanel" id="panel-ws">
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">PC WORKSTATION 2D</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">Photoshop, Illustrator, Corel</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">AutoCAD Kiến Trúc Xây Dựng</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">PC WORKSTATION 3D</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">3ds Max, Maya, Blender, Vray</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">SketchUp, Lumion Render Realtime</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">PC DỰNG PHIM - EDIT VIDEO</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">Adobe Premiere Pro 4K</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">After Effects Kỹ Xảo Điện Ảnh</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">DaVinci Resolve Chỉnh Màu</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-workstation') ?>">WORKSTATION THEO GIÁ</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-workstation&price_range[]=20-25tr') ?>">Từ 20 - 25 Triệu</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-workstation&price_range[]=35-45tr') ?>">Từ 35 - 50 Triệu</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-workstation&price_range[]=tren-60tr') ?>">Cao Cấp Trên 60 Triệu</a></li>
                    </ul>
                </div>
            </div>

            <!-- 3. PANEL PC AMD -->
            <div class="pcm-mega-subpanel" id="panel-amd">
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">AMD RYZEN 5</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming&cpu[]=Ryzen-5') ?>">Ryzen 5 5500 / 5600</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming&cpu[]=Ryzen-5') ?>">Ryzen 5 7500F (AM5)</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">AMD RYZEN 7</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming&cpu[]=Ryzen-7') ?>">Ryzen 7 5700X Gaming</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming&cpu[]=Ryzen-7') ?>">Ryzen 7 7800X3D (Vua Gaming)</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming&cpu[]=Ryzen-7') ?>">Ryzen 7 9800X3D (Thế Hệ Mới)</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">AMD RYZEN 9</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming&cpu[]=Ryzen-9') ?>">Ryzen 9 7900X / 7950X</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming&cpu[]=Ryzen-9') ?>">Ryzen 9 9950X3D Siêu Đẳng</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">SOCKET & BO MẠCH</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">Nền tảng AM4 Tiết Kiệm</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-amd-gaming') ?>">Nền tảng AM5 Tốc Độ Cao</a></li>
                    </ul>
                </div>
            </div>

            <!-- 4. PANEL LINH KIỆN MÁY TÍNH -->
            <div class="pcm-mega-subpanel" id="panel-parts">
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=cpu-vi-xu-ly') ?>">CPU - BỘ VI XỬ LÝ</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=cpu-vi-xu-ly&brand[]=intel') ?>">Intel Core i5 / i7 / i9</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=cpu-vi-xu-ly&brand[]=intel') ?>">Intel Core Ultra 5 / 7 / 9</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=cpu-vi-xu-ly&brand[]=amd') ?>">AMD Ryzen 5 / 7 / 9 3D V-Cache</a></li>
                    </ul>
                    <h4 class="pcm-mega-group-title" style="margin-top: 14px;"><a href="<?= base_url('san-pham?danh-muc=mainboard-bo-mach-chu') ?>">BO MẠCH CHỦ (MAINBOARD)</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=mainboard-bo-mach-chu') ?>">Intel B760 / Z790 DDR5</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=mainboard-bo-mach-chu') ?>">AMD B650 / X670 AM5</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=vga-card-man-hinh') ?>">VGA - CARD MÀN HÌNH</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=vga-card-man-hinh') ?>">GeForce RTX 3050 / 3060 12GB</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=vga-card-man-hinh') ?>">GeForce RTX 4060 / 4070 Super</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=vga-card-man-hinh') ?>">GeForce RTX 5050 / 5060 Ti</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=vga-card-man-hinh') ?>">GeForce RTX 5070 / 5080 / 5090</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=ram-bo-nho-trong') ?>">BỘ NHỚ TRONG (RAM)</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=ram-bo-nho-trong') ?>">RAM DDR4 16GB / 32GB</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=ram-bo-nho-trong') ?>">RAM DDR5 6000MHz RGB</a></li>
                    </ul>
                    <h4 class="pcm-mega-group-title" style="margin-top: 14px;"><a href="<?= base_url('san-pham?danh-muc=o-cung-ssd-hdd') ?>">Ổ CỨNG SSD / HDD</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=o-cung-ssd-hdd') ?>">SSD NVMe Gen4 512GB - 1TB</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=o-cung-ssd-hdd') ?>">SSD NVMe Gen5 7000MB/s</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=linh-kien-may-tinh') ?>">NGUỒN, VỎ CASE & TẢN NHIỆT</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=nguon-may-tinh-psu') ?>">Nguồn 650W / 750W / 850W 80 Plus</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=vo-case-may-tinh') ?>">Vỏ Case Bể Cá Kính Cường Lực</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=tan-nhiet-cooling') ?>">Tản Nhiệt Khí & Nước AIO ARGB</a></li>
                    </ul>
                </div>
            </div>

            <!-- 5. PANEL PC VĂN PHÒNG -->
            <div class="pcm-mega-subpanel" id="panel-office">
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">PC DOANH NGHIỆP</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">Bảo Mật & Hoạt Động Bền Bỉ 24/7</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">Windows 11 Pro Bản Quyền</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">PC KẾ TOÁN & VĂN PHÒNG</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">Xử lý file Excel nặng mượt mà</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">Khởi động máy trong 5 giây</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">COMBO KÈM MÀN HÌNH</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">PC M Office + Màn hình 22 Inch</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">PC M Office + Màn hình IPS 24 Inch</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-van-phong') ?>">PHÂN KHÚC GIÁ</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-van-phong&price_range[]=duoi-10tr') ?>">PC Dưới 10 Triệu</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-van-phong&price_range[]=10-15tr') ?>">PC Từ 10 - 15 Triệu</a></li>
                    </ul>
                </div>
            </div>

            <!-- 6. PANEL MÀN HÌNH -->
            <div class="pcm-mega-subpanel" id="panel-monitor">
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">THƯƠNG HIỆU HÀNG ĐẦU</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh&brand[]=lg') ?>">LG UltraGear Gaming</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh&brand[]=asus') ?>">ASUS TUF / ROG Strix</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh&brand[]=msi') ?>">MSI MAG Esports</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh&brand[]=aoc') ?>">AOC Gaming Fast IPS</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">TẦN SỐ QUÉT</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">100Hz Mượt Mà Văn Phòng</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">180Hz - 240Hz Game Bắn Súng</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">300Hz Đỉnh Cao Thi Đấu</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">KÍCH THƯỚC MÀN HÌNH</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">24 Inch Chuẩn Esport</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">27 Inch 2K QHD Rộng Rãi</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">32 Inch Màn Cong Đắm Chìm</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">TẤM NỀN</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">Tấm Nền IPS Góc Nhìn Rộng</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=man-hinh-may-tinh') ?>">Fast IPS / Nano IPS 1ms</a></li>
                    </ul>
                </div>
            </div>

            <!-- 7. PANEL PC AI -->
            <div class="pcm-mega-subpanel" id="panel-ai">
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">AI LOCAL STARTER</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">Chạy LLaMA 3, Whisper, TTS</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">Stable Diffusion Vẽ Tranh AI</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">AI LOCAL CREATOR</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">VRAM 16GB Huấn Luyện LoRA</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">Core Ultra NPU Tăng Tốc AI</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">AI LOCAL PRO DOANH NGHIỆP</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">Chạy Mô Hình Offline Bảo Mật 100%</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">Trang bị RTX 5080 / 5090 Blackwell</a></li>
                    </ul>
                </div>
                <div class="pcm-mega-col">
                    <h4 class="pcm-mega-group-title"><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">TƯ VẤN CẤU HÌNH AI</a></h4>
                    <ul>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">Cấu hình tối ưu theo VRAM</a></li>
                        <li><a href="<?= base_url('san-pham?danh-muc=pc-ai-tri-tue-nhan-tao') ?>">Hỗ trợ cài đặt LLM miễn phí</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
