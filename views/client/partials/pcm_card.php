<?php
/**
 * PCM Standard Product Card (Chuẩn giao diện siêu thị PC chuyên nghiệp)
 * @var array $item
 */
$regPrice = (float)($item['price'] ?? 0);
$salePrice = (float)($item['discount_price'] ?? 0);
$hasSale = $salePrice > 0 && $salePrice < $regPrice;
$currentPrice = $hasSale ? $salePrice : $regPrice;
$discountPercent = $hasSale ? round((1 - ($salePrice / $regPrice)) * 100) : 0;
$productUrl = base_url('san-pham/' . rawurlencode($item['slug'] ?? ''));
$thumbnail = !empty($item['thumbnail']) ? asset($item['thumbnail']) : asset('images/product-placeholder.svg');
if (str_starts_with($item['thumbnail'] ?? '', 'http')) {
    $thumbnail = $item['thumbnail'];
}
?>
<div class="pcm-card" data-product-id="<?= (int)($item['id'] ?? 0) ?>">
    <?php if ($hasSale): ?>
        <div class="pcm-ribbon-discount">-<?= $discountPercent ?>%</div>
    <?php endif; ?>
    
    <a class="pcm-card-thumb" href="<?= $productUrl ?>" title="<?= htmlspecialchars($item['name'] ?? '') ?>">
        <img src="<?= htmlspecialchars($thumbnail) ?>" 
             alt="<?= htmlspecialchars($item['name'] ?? '') ?>" 
             loading="lazy" 
             width="180" 
             height="180">
    </a>

    <div class="pcm-card-content">
        <h3 class="pcm-card-title">
            <a href="<?= $productUrl ?>" title="<?= htmlspecialchars($item['name'] ?? '') ?>">
                <?= htmlspecialchars($item['name'] ?? '') ?>
            </a>
        </h3>

        <div class="pcm-card-bottom">
            <div class="pcm-price-box">
                <span class="pcm-price-current"><?= number_format($currentPrice, 0, ',', '.') ?> VNĐ</span>
                <?php if ($hasSale): ?>
                    <span class="pcm-price-old"><?= number_format($regPrice, 0, ',', '.') ?> VNĐ</span>
                <?php endif; ?>
            </div>
            
            <button type="button" 
                    class="pcm-cart-btn" 
                    data-pcm-add-cart="<?= (int)($item['id'] ?? 0) ?>" 
                    data-name="<?= htmlspecialchars($item['name'] ?? '') ?>"
                    aria-label="Thêm vào giỏ hàng">
                <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>
