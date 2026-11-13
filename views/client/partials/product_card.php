<?php
$regularPrice = (float)($item['price'] ?? 0);
$salePrice = (float)($item['discount_price'] ?? 0);
$hasDiscount = $salePrice > 0 && $salePrice < $regularPrice;
$specifications = json_decode($item['specifications'] ?? '{}', true) ?: [];
$specKeys = ['cpu' => 'CPU', 'vga' => 'VGA', 'ram' => 'RAM', 'storage' => 'SSD', 'socket' => 'Socket', 'capacity' => 'Dung lượng', 'speed' => 'Tốc độ', 'vram' => 'VRAM', 'cores' => 'Nhân'];
$cardSpecs = [];
foreach ($specKeys as $key => $label) { if (!empty($specifications[$key]) && is_scalar($specifications[$key])) $cardSpecs[$label] = $specifications[$key]; if (count($cardSpecs) >= 3) break; }
?>
<article class="shop-product">
    <a class="product-image" href="<?= base_url('san-pham/' . rawurlencode($item['slug'])) ?>" tabindex="-1" aria-hidden="true">
        <img src="<?= htmlspecialchars(product_image_url($item['thumbnail'] ?? '')) ?>" 
             alt="<?= htmlspecialchars($item['name'] ?? '') ?>" 
             width="520" 
             height="400" 
             loading="lazy" 
             data-product-image
             onerror="this.onerror=null; this.src='<?= htmlspecialchars(asset('images/product-placeholder.svg'), ENT_QUOTES, 'UTF-8') ?>';">
        <?php if ($hasDiscount): ?><span class="product-discount">Giảm <?= round((1 - $salePrice / $regularPrice) * 100) ?>%</span><?php endif; ?>
        <span class="image-caption">Ảnh minh họa</span>
    </a>
    <div class="product-body"><div class="product-meta"><span><?= htmlspecialchars($item['brand_name'] ?? $item['sku'] ?? 'PCMarket') ?></span><span class="<?= (int)($item['quantity'] ?? 0) > 0 ? 'in-stock' : '' ?>"><?= (int)($item['quantity'] ?? 0) > 0 ? '● Còn hàng' : 'Hết hàng' ?></span></div>
    <h3><a href="<?= base_url('san-pham/' . rawurlencode($item['slug'])) ?>"><?= htmlspecialchars($item['name']) ?></a></h3>
    <?php if ($cardSpecs): ?><dl class="product-spec-list"><?php foreach ($cardSpecs as $label => $value): ?><div><dt><?= $label ?></dt><dd><?= htmlspecialchars($value) ?></dd></div><?php endforeach; ?></dl><?php endif; ?>
    <div class="product-prices"><?php if ($hasDiscount): ?><s><?= format_money($regularPrice) ?></s><?php endif; ?><strong><?= format_money($hasDiscount ? $salePrice : $regularPrice) ?></strong></div><a class="product-details-link" href="<?= base_url('san-pham/' . rawurlencode($item['slug'])) ?>"><?= (int)($item['category_id'] ?? 1) <= 3 ? 'Xem cấu hình' : 'Xem sản phẩm' ?> <span aria-hidden="true">→</span></a><?php $compareProduct = $item; require VIEWS_DIR . '/client/partials/pc_compare_button.php'; ?></div>
</article>
