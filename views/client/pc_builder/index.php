<?php
require VIEWS_DIR . '/layouts/client_header.php';
$types = [
    'cpu'         => ['CPU', 'Bộ xử lý', 'fa-microchip'],
    'motherboard' => ['Mainboard', 'Bo mạch chủ', 'fa-server'],
    'ram'         => ['RAM', 'Bộ nhớ trong', 'fa-memory'],
    'gpu'         => ['VGA', 'Card đồ họa', 'fa-display'],
    'storage'     => ['SSD / HDD', 'Lưu trữ', 'fa-hard-drive'],
    'psu'         => ['PSU', 'Nguồn máy tính', 'fa-plug'],
    'case'        => ['Case', 'Vỏ máy tính', 'fa-computer'],
    'cooler'      => ['Cooling', 'Tản nhiệt CPU', 'fa-fan'],
    'monitor'     => ['Màn hình', 'Hiển thị (Tùy chọn)', 'fa-tv'],
];
$builderData = [];
foreach ($components as $type => $items) {
    $builderData[$type] = array_map(static function ($p) {
        return [
            'id'        => (int)$p['id'],
            'name'      => $p['name'],
            'sku'       => $p['sku'],
            'price'     => (float)$p['current_price'],
            'stock'     => (int)$p['quantity'],
            'thumbnail' => product_image_url($p['thumbnail'] ?? ''),
            'specs'     => $p['specs'] ?? [],
        ];
    }, $items);
}
?>
<link rel="stylesheet" href="<?= asset('css/pc-builder.css') ?>?v=3">
<section class="page-width pcb-page" data-placeholder="<?= htmlspecialchars(asset('images/product-placeholder.svg'), ENT_QUOTES, 'UTF-8') ?>">
 <div class="pcb-hero">
  <div>
   <span class="eyebrow">PCMARKET / CUSTOM BUILD</span>
   <h1>XÂY DỰNG CẤU HÌNH PC</h1>
   <p>Tự chọn linh kiện, kiểm tra tự động tương thích Socket, RAM, kích thước Case và công suất nguồn PSU theo thời gian thực.</p>
  </div>
  <div class="pcb-hero-mark"><i class="fa-solid fa-screwdriver-wrench"></i><span>BUILD YOUR PC</span></div>
 </div>
 <div class="pcb-layout">
  <section class="pcb-components" aria-label="Chọn linh kiện">
   <div class="pcb-section-heading">
    <div><span class="eyebrow">COMPONENT SELECTOR</span><h2>Chọn từng linh kiện</h2></div>
    <button class="button button-secondary" type="button" id="pcb-clear">Làm mới</button>
   </div>
   <div class="pcb-slot-list">
   <?php foreach ($types as $key => $meta): ?>
    <article class="pcb-slot" data-slot="<?= htmlspecialchars($key) ?>">
     <div class="pcb-slot-icon"><i class="fa-solid <?= $meta[2] ?>"></i></div>
     <div class="pcb-slot-info">
      <strong><?= htmlspecialchars($meta[0]) ?></strong>
      <span><?= htmlspecialchars($meta[1]) ?></span>
      <div class="pcb-selected" data-selected-label>Chưa chọn linh kiện</div>
     </div>
     <div class="pcb-slot-action">
      <strong data-selected-price>—</strong>
      <div style="display:flex;gap:6px;align-items:center;">
       <button type="button" class="button button-outline pcb-choose" data-choose="<?= htmlspecialchars($key) ?>">Chọn</button>
       <button type="button" class="icon-button pcb-remove" data-remove="<?= htmlspecialchars($key) ?>" aria-label="Bỏ chọn <?= htmlspecialchars($meta[0]) ?>" title="Bỏ chọn" hidden style="color:#dc2626;border:1px solid #fecaca;border-radius:6px;padding:4px 8px;">×</button>
      </div>
     </div>
    </article>
   <?php endforeach; ?>
   </div>
   <div class="pcb-notice" id="pcb-compatibility" role="status" aria-live="polite">Chọn CPU, Mainboard, RAM, VGA, PSU và Case để hệ thống kiểm tra tự động độ tương thích &amp; công suất nguồn.</div>
  </section>
  <aside class="pcb-summary" aria-label="Tóm tắt cấu hình">
   <span class="eyebrow">BUILD SUMMARY</span>
   <h2>Cấu hình của bạn</h2>
   <div class="pcb-summary-count"><strong id="pcb-count">0/9</strong><span>linh kiện đã chọn</span></div>
   <div class="pcb-power-box" id="pcb-power-box" hidden>
    <span><i class="fa-solid fa-bolt" aria-hidden="true"></i> Công suất ước tính</span>
    <strong id="pcb-power-est">0W</strong>
   </div>
   <div class="pcb-total-label">TỔNG TẠM TÍNH</div>
   <strong class="pcb-total" id="pcb-total">0 ₫</strong>
   <p class="pcb-summary-note">Giá và tồn kho sẽ được máy chủ kiểm tra lại khi thêm vào giỏ hàng.</p>
   <button type="button" class="button button-primary pcb-add-cart" id="pcb-add-cart">Thêm cả bộ vào giỏ</button>
   <button type="button" class="button button-secondary pcb-print" id="pcb-print">In báo giá</button>
   <div id="pcb-feedback" class="pcb-feedback" role="status" aria-live="polite"></div>
  </aside>
 </div>
</section>
<dialog class="pcb-picker" id="pcb-picker">
 <div class="pcb-picker-head">
  <div><span class="eyebrow">COMPONENT CATALOG</span><h2 id="pcb-picker-title">Chọn linh kiện</h2></div>
  <button class="icon-button" type="button" id="pcb-picker-close" aria-label="Đóng">×</button>
 </div>
 <div class="pcb-picker-toolbar">
  <input type="search" id="pcb-picker-search" placeholder="Tìm nhanh theo tên, hãng, socket, DDR4/DDR5, công suất..." autocomplete="off">
  <span class="pcb-picker-count" id="pcb-picker-count"></span>
 </div>
 <div class="pcb-picker-list" id="pcb-picker-list"></div>
</dialog>
<script type="application/json" id="pcb-data"><?= json_encode($builderData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE) ?></script>
<script src="<?= asset('js/pc-builder.js') ?>?v=3" defer></script>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
