<?php
$regularPrice = (float)($item['price'] ?? 0);
$salePrice = (float)($item['discount_price'] ?? 0);
$hasDiscount = $salePrice > 0 && $salePrice < $regularPrice;
$specifications = json_decode($item['specifications'] ?? '{}', true) ?: [];
$shortSpecs = array_slice(array_filter([$specifications['cpu'] ?? null, $specifications['ram'] ?? $specifications['capacity'] ?? null, $specifications['vga'] ?? $specifications['socket'] ?? null]), 0, 2);
?>
<article class="shop-product">
    <a class="product-image" href="<?= base_url('san-pham/' . rawurlencode($item['slug'])) ?>" tabindex="-1" aria-hidden="true">
        <img src="<?= htmlspecialchars($item['thumbnail'] ?? '') ?>" alt="" width="520" height="400" loading="lazy" data-product-image>
        <?php if ($hasDiscount): ?><span class="product-discount">−<?= round((1 - $salePrice / $regularPrice) * 100) ?>%</span><?php endif; ?>
        <span class="product-image-arrow" aria-hidden="true">↗</span>
    </a>
    <div class="product-meta"><span><?= htmlspecialchars($item['brand_name'] ?? $item['sku'] ?? 'PCMarket') ?></span><span><?= (int)($item['quantity'] ?? 0) > 0 ? 'Còn hàng' : 'Hết hàng' ?></span></div>
    <h3><a href="<?= base_url('san-pham/' . rawurlencode($item['slug'])) ?>"><?= htmlspecialchars($item['name']) ?></a></h3>
    <?php if ($shortSpecs): ?><p class="product-spec-line"><?= htmlspecialchars(implode(' · ', $shortSpecs)) ?></p><?php endif; ?>
    <div class="product-prices"><strong><?= format_money($hasDiscount ? $salePrice : $regularPrice) ?></strong><?php if ($hasDiscount): ?><s><?= format_money($regularPrice) ?></s><?php endif; ?></div>
</article>
