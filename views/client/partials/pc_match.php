<?php
$matchProducts = [];
foreach (array_merge($gamingPCs, $featuredProducts) as $matchItem) {
    if ((int)($matchItem['category_id'] ?? 0) < 1 || (int)$matchItem['category_id'] > 3 || (int)($matchItem['quantity'] ?? 0) < 1) continue;
    $matchRegular = (float)$matchItem['price'];
    $matchSale = (float)($matchItem['discount_price'] ?? 0);
    $matchProducts[(int)$matchItem['id']] = [
        'id' => (int)$matchItem['id'], 'name' => $matchItem['name'], 'category' => (int)$matchItem['category_id'], 'quantity' => (int)$matchItem['quantity'],
        'price' => $matchSale > 0 && $matchSale < $matchRegular ? $matchSale : $matchRegular,
        'image' => $matchItem['thumbnail'], 'url' => base_url('san-pham/' . rawurlencode($matchItem['slug'])),
        'specs' => json_decode($matchItem['specifications'] ?? '{}', true) ?: [],
    ];
}
?>
<section class="pc-match" id="pc-match" aria-labelledby="match-title">
    <div class="match-heading"><div><span class="match-kicker"><i class="fa-solid fa-sliders" aria-hidden="true"></i> BÀN CHỌN CẤU HÌNH</span><h2 id="match-title">ĐỪNG ĐOÁN HIỆU NĂNG.<br><span>ĐẶT CẤU HÌNH LÊN BÀN.</span></h2><p>Chọn nhu cầu, kéo ngân sách. So sánh từng linh kiện trước khi quyết định.</p></div><span class="match-heading-note">Dữ liệu từ cấu hình nổi bật đang còn hàng.<br>Không sử dụng điểm hiệu năng ước tính.</span></div>
    <div class="match-workspace" data-pc-match hidden>
        <div class="match-controls">
            <fieldset class="match-needs"><legend>Bạn cần máy để</legend><label><input type="radio" name="match-need" value="0" checked><span>Tất cả PC</span></label><label><input type="radio" name="match-need" value="1"><span>Gaming / Stream</span></label><label><input type="radio" name="match-need" value="2"><span>Đồ họa / Render</span></label><label><input type="radio" name="match-need" value="3"><span>Văn phòng</span></label></fieldset>
            <div class="match-budget"><div><label for="match-budget">Ngân sách tối đa</label><output for="match-budget" id="match-budget-value">60.000.000 ₫</output></div><input id="match-budget" type="range" min="10" max="80" step="1" value="60" aria-describedby="match-budget-help"><p id="match-budget-help"><span>10 triệu</span><span>80 triệu</span></p></div>
            <p class="match-rule"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Lọc theo nhóm máy và giá bán. Gaming / Stream dùng chung nhóm PC Gaming; chưa có đánh giá riêng cho từng phần mềm.</p>
        </div>
        <div class="match-results-area"><div class="match-results-heading"><h3>Cấu hình trong tầm giá</h3><span data-match-count role="status" aria-live="polite"></span></div><div class="match-results" data-match-results></div><div class="match-empty" data-match-empty hidden><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><h3>Chưa có cấu hình phù hợp.</h3><p>Thử tăng ngân sách hoặc chọn nhóm máy khác.</p><button type="button" class="button button-outline" data-match-reset>Xem lại tất cả PC nổi bật</button></div></div>
    </div>
    <noscript><p class="match-noscript">Bật JavaScript để lọc và so sánh cấu hình, hoặc <a href="<?= base_url('san-pham') ?>">xem tất cả sản phẩm →</a>.</p></noscript>
    <div class="match-comparison" data-match-comparison hidden><div class="match-compare-heading"><div><h3>Danh sách so sánh dùng chung</h3><p data-match-selection-note>Chọn 2–4 PC từ trang chủ hoặc cửa hàng.</p></div><button type="button" class="button button-primary" data-compare-open disabled>Mở bảng so sánh</button></div><button type="button" class="text-button" data-match-clear>Bỏ chọn tất cả</button></div>
    <script type="application/json" id="pc-match-data"><?= json_encode(array_values($matchProducts), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
    <script src="<?= asset('js/pc-match.js') ?>?v=2" defer></script>
</section>
