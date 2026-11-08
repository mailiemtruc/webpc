<?php
require VIEWS_DIR . '/layouts/client_header.php';

$curCat = $categorySlug ?? '';
$selectedPriceRanges = (array)($params['price_range'] ?? []);
$selectedBrands = (array)($params['brand'] ?? []);
$selectedCpus = (array)($params['cpu'] ?? []);
$selectedRams = (array)($params['ram'] ?? []);
$selectedVgas = (array)($params['vga'] ?? []);
$selectedSort = $params['sort'] ?? 'newest';
$selectedSubCat = $params['sub_cat'] ?? '';
$searchQ = $params['q'] ?? '';

// Build active filter list for the tags bar
$activeFilters = [];
if (!empty($selectedPriceRanges)) {
    foreach ($selectedPriceRanges as $pr) {
        $label = $facets['price_ranges'][$pr]['label'] ?? $pr;
        $activeFilters[] = ['type' => 'price_range', 'val' => $pr, 'label' => $label];
    }
}
if (!empty($selectedBrands)) {
    foreach ($selectedBrands as $b) {
        $label = $facets['brands'][$b]['name'] ?? $b;
        $activeFilters[] = ['type' => 'brand', 'val' => $b, 'label' => 'Hãng: ' . $label];
    }
}
if (!empty($selectedCpus)) {
    foreach ($selectedCpus as $c) {
        $label = $facets['cpus'][$c]['label'] ?? $c;
        $activeFilters[] = ['type' => 'cpu', 'val' => $c, 'label' => 'CPU: ' . $label];
    }
}
if (!empty($selectedRams)) {
    foreach ($selectedRams as $r) {
        $activeFilters[] = ['type' => 'ram', 'val' => $r, 'label' => 'RAM: ' . $r];
    }
}
if (!empty($selectedVgas)) {
    foreach ($selectedVgas as $v) {
        $label = $facets['vgas'][$v]['label'] ?? $v;
        $activeFilters[] = ['type' => 'vga', 'val' => $v, 'label' => 'VGA: ' . $label];
    }
}
if ($selectedSubCat !== '') {
    $activeFilters[] = ['type' => 'sub_cat', 'val' => $selectedSubCat, 'label' => 'Phân loại: ' . $selectedSubCat];
}
if ($searchQ !== '') {
    $activeFilters[] = ['type' => 'q', 'val' => $searchQ, 'label' => 'Tìm: "' . $searchQ . '"'];
}

// Subcategory definitions for specific main categories
$subCategories = [
    'pc-gaming' => [
        'MÁY TÍNH CHƠI GAME PCM',
        'PC ĐẸP - LUXURY',
        'PC GAMING PREMIUM',
        'PC Core Ultra',
        'PC STREAMER, YOUTUBER',
        'PC GAMING DDR5',
        'Full Bộ PC Kèm Màn Hình'
    ],
    'pc-workstation' => [
        'PC WORKSTATION 2D',
        'PC WORKSTATION 3D',
        'PC Dựng Phim - Edit Video',
        'Workstation Render 8K'
    ],
    'pc-amd-gaming' => [
        'AMD Ryzen 5',
        'AMD Ryzen 7',
        'AMD Ryzen 9 3D V-Cache',
        'AM5 Socket'
    ],
    'pc-van-phong' => [
        'PC DOANH NGHIỆP - Windows Bản Quyền',
        'PC Văn Phòng Theo Nhu Cầu',
        'PC Kế Toán Xử Lý Dữ Liệu',
        'PC Trọn Bộ M OFFICE'
    ],
    'man-hinh-may-tinh' => [
        'Màn Hình Gaming 144Hz - 300Hz',
        'Màn Hình Đồ Họa 2K/4K Nano IPS',
        'Màn Hình Văn Phòng 24 - 27 Inch'
    ],
    'pc-ai-tri-tue-nhan-tao' => [
        'AI Local Starter',
        'AI Local Creator',
        'LLaMA & Stable Diffusion Offline',
        'VRAM Khủng 16GB - 24GB'
    ],
    'linh-kien-may-tinh' => [
        'CPU - Bộ vi xử lý',
        'Mainboard - Bo mạch chủ',
        'VGA - Card màn hình',
        'RAM - Bộ nhớ trong',
        'Case - Vỏ máy tính',
        'PSU - Nguồn máy tính',
        'Ổ cứng SSD / HDD',
        'TẢN NHIỆT - COOLING'
    ]
];
$currentSubCats = $subCategories[$curCat] ?? [];
?>

<div class="pcm-catalog-wrapper">
    <div class="page-width">
        <!-- BREADCRUMB -->
        <nav class="pcm-breadcrumb" aria-label="Đường dẫn">
            <a href="<?= base_url() ?>">Trang chủ</a>
            <span>/</span>
            <a href="<?= base_url('san-pham') ?>">Sản phẩm</a>
            <?php if ($curCat): ?>
                <span>/</span>
                <span class="pcm-breadcrumb-active"><?= htmlspecialchars($pageTitle) ?></span>
            <?php endif; ?>
        </nav>

        <div class="pcm-catalog-layout">
            <!-- SIDEBAR BỘ LỌC CHUYÊN SÂU (CHUẨN PCMARKET) -->
            <aside class="pcm-filter-sidebar">
                <form id="pcmFilterForm" action="<?= base_url('san-pham') ?>" method="GET">
                    <?php if ($curCat): ?>
                        <input type="hidden" name="danh-muc" value="<?= htmlspecialchars($curCat) ?>">
                    <?php endif; ?>
                    <?php if ($searchQ): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($searchQ) ?>">
                    <?php endif; ?>

                    <div class="pcm-sidebar-btn-head">
                        <i class="fa-solid fa-filter" aria-hidden="true"></i> LỌC SẢN PHẨM
                    </div>

                    <!-- 1. DANH MỤC CON / NHU CẦU THEO TRANG -->
                    <?php if (!empty($currentSubCats)): ?>
                        <div class="pcm-filter-group">
                            <h3 class="pcm-filter-group-title"><?= htmlspecialchars($pageTitle) ?></h3>
                            <ul class="pcm-subcat-list">
                                <?php foreach ($currentSubCats as $sc): ?>
                                    <li>
                                        <a href="<?= base_url('san-pham?' . http_build_query(array_merge($params, ['sub_cat' => $sc, 'page' => 1]))) ?>" 
                                           class="<?= $selectedSubCat === $sc ? 'is-active' : '' ?>">
                                            » <?= htmlspecialchars($sc) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- 2. BỘ LỌC KHOẢNG GIÁ -->
                    <div class="pcm-filter-group">
                        <h3 class="pcm-filter-group-title">KHOẢNG GIÁ</h3>
                        <div class="pcm-checkbox-list">
                            <?php foreach ($facets['price_ranges'] as $pKey => $pData): ?>
                                <label class="pcm-checkbox-item">
                                    <input type="checkbox" 
                                           name="price_range[]" 
                                           value="<?= $pKey ?>" 
                                           <?= in_array($pKey, $selectedPriceRanges, true) ? 'checked' : '' ?>
                                           onchange="this.form.submit()">
                                    <span class="pcm-chk-label"><?= $pData['label'] ?></span>
                                    <span class="pcm-chk-count">(<?= $pData['count'] ?>)</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 3. BỘ LỌC HÃNG SẢN XUẤT -->
                    <?php if (!empty($facets['brands'])): ?>
                        <div class="pcm-filter-group">
                            <h3 class="pcm-filter-group-title">HÃNG SẢN XUẤT</h3>
                            <div class="pcm-checkbox-list">
                                <?php foreach ($facets['brands'] as $bSlug => $bData): ?>
                                    <label class="pcm-checkbox-item">
                                        <input type="checkbox" 
                                               name="brand[]" 
                                               value="<?= $bSlug ?>" 
                                               <?= in_array($bSlug, $selectedBrands, true) ? 'checked' : '' ?>
                                               onchange="this.form.submit()">
                                        <span class="pcm-chk-label"><?= htmlspecialchars($bData['name']) ?></span>
                                        <span class="pcm-chk-count">(<?= $bData['count'] ?>)</span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 4. BỘ LỌC CPU -->
                    <?php if (!empty($facets['cpus'])): ?>
                        <div class="pcm-filter-group">
                            <h3 class="pcm-filter-group-title">VI XỬ LÝ (CPU)</h3>
                            <div class="pcm-checkbox-list">
                                <?php foreach ($facets['cpus'] as $cKey => $cData): ?>
                                    <label class="pcm-checkbox-item">
                                        <input type="checkbox" 
                                               name="cpu[]" 
                                               value="<?= $cKey ?>" 
                                               <?= in_array($cKey, $selectedCpus, true) ? 'checked' : '' ?>
                                               onchange="this.form.submit()">
                                        <span class="pcm-chk-label"><?= htmlspecialchars($cData['label']) ?></span>
                                        <span class="pcm-chk-count">(<?= $cData['count'] ?>)</span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 5. BỘ LỌC RAM -->
                    <?php if (!empty($facets['rams'])): ?>
                        <div class="pcm-filter-group">
                            <h3 class="pcm-filter-group-title">BỘ NHỚ (RAM)</h3>
                            <div class="pcm-checkbox-list">
                                <?php foreach ($facets['rams'] as $rKey => $rData): ?>
                                    <label class="pcm-checkbox-item">
                                        <input type="checkbox" 
                                               name="ram[]" 
                                               value="<?= $rKey ?>" 
                                               <?= in_array($rKey, $selectedRams, true) ? 'checked' : '' ?>
                                               onchange="this.form.submit()">
                                        <span class="pcm-chk-label"><?= htmlspecialchars($rData['label']) ?></span>
                                        <span class="pcm-chk-count">(<?= $rData['count'] ?>)</span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 6. BỘ LỌC VGA -->
                    <?php if (!empty($facets['vgas'])): ?>
                        <div class="pcm-filter-group">
                            <h3 class="pcm-filter-group-title">CARD ĐỒ HỌA (VGA)</h3>
                            <div class="pcm-checkbox-list">
                                <?php foreach ($facets['vgas'] as $vKey => $vData): ?>
                                    <label class="pcm-checkbox-item">
                                        <input type="checkbox" 
                                               name="vga[]" 
                                               value="<?= $vKey ?>" 
                                               <?= in_array($vKey, $selectedVgas, true) ? 'checked' : '' ?>
                                               onchange="this.form.submit()">
                                        <span class="pcm-chk-label"><?= htmlspecialchars($vData['label']) ?></span>
                                        <span class="pcm-chk-count">(<?= $vData['count'] ?>)</span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </form>
            </aside>

            <!-- KHU VỰC SẢN PHẨM CHÍNH -->
            <main class="pcm-catalog-main">
                <!-- TOOLBAR -->
                <div class="pcm-catalog-toolbar">
                    <div class="pcm-toolbar-count">
                        Tìm thấy <strong><?= number_format($total) ?></strong> sản phẩm
                    </div>

                    <div class="pcm-toolbar-sort">
                        <label for="pcmSortSelect">Sắp xếp theo:</label>
                        <select id="pcmSortSelect" onchange="applySort(this.value)">
                            <option value="newest" <?= $selectedSort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price-asc" <?= $selectedSort === 'price-asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                            <option value="price-desc" <?= $selectedSort === 'price-desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                            <option value="popular" <?= $selectedSort === 'popular' ? 'selected' : '' ?>>Xem nhiều nhất</option>
                        </select>
                    </div>
                </div>

                <!-- THANH BỘ LỌC ĐANG CHỌN (ACTIVE FILTER TAGS) -->
                <?php if (!empty($activeFilters)): ?>
                    <div class="pcm-active-tags-bar">
                        <span class="pcm-active-tags-label">Đang lọc:</span>
                        <?php foreach ($activeFilters as $af): ?>
                            <?php
                                // Link to remove this specific filter
                                $removeParams = $params;
                                if (is_array($removeParams[$af['type']] ?? null)) {
                                    $removeParams[$af['type']] = array_diff($removeParams[$af['type']], [$af['val']]);
                                } else {
                                    unset($removeParams[$af['type']]);
                                }
                                $removeUrl = base_url('san-pham?' . http_build_query($removeParams));
                            ?>
                            <a class="pcm-filter-tag" href="<?= $removeUrl ?>">
                                <?= htmlspecialchars($af['label']) ?> <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php endforeach; ?>
                        
                        <a class="pcm-clear-all-tag" href="<?= base_url('san-pham' . ($curCat ? '?danh-muc=' . urlencode($curCat) : '')) ?>">
                            Xóa tất cả bộ lọc
                        </a>
                    </div>
                <?php endif; ?>

                <!-- LƯỚI SẢN PHẨM 5 CỘT CHUẨN PCMARKET -->
                <?php if (!empty($products)): ?>
                    <div class="pcm-catalog-grid">
                        <?php foreach ($products as $item): ?>
                            <?php require VIEWS_DIR . '/client/partials/pcm_card.php'; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- PHÂN TRANG -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pcm-pagination-wrap">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <?php $pageUrl = base_url('san-pham?' . http_build_query(array_merge($params, ['page' => $p]))); ?>
                                <a href="<?= $pageUrl ?>" class="pcm-page-link <?= $p === (int)$page ? 'is-active' : '' ?>">
                                    <?= $p ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="pcm-empty-catalog">
                        <i class="fa-solid fa-box-open" aria-hidden="true"></i>
                        <h3>Không tìm thấy sản phẩm phù hợp với bộ lọc</h3>
                        <p>Bạn hãy thử giảm bớt các tiêu chí lọc hoặc xóa bộ lọc để xem toàn bộ sản phẩm.</p>
                        <a href="<?= base_url('san-pham' . ($curCat ? '?danh-muc=' . urlencode($curCat) : '')) ?>" class="pcm-btn-reset-filters">
                            Xóa bộ lọc và tải lại
                        </a>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</div>

<script>
function applySort(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', val);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
</script>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
