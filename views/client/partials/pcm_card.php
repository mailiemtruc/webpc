
<?php
/**
 * PCM Standard Product Card
 * @var array $item
 */

// Giá sản phẩm
$regPrice = (float)($item['price'] ?? 0);
$salePrice = (float)($item['discount_price'] ?? 0);

$hasSale = $salePrice > 0 && $salePrice < $regPrice;
$currentPrice = $hasSale ? $salePrice : $regPrice;

$discountPercent = $hasSale
    ? round((1 - ($salePrice / $regPrice)) * 100)
    : 0;


// Thông tin sản phẩm
$productId = (int)($item['id'] ?? 0);
$productName = $item['name'] ?? '';
$productSlug = $item['slug'] ?? '';


// Đường dẫn chi tiết sản phẩm
$productUrl = base_url(
    'san-pham/' . rawurlencode($productSlug)
);


// Ảnh mặc định
$placeholder = asset('images/product-placeholder.svg');


// Xử lý ảnh sản phẩm
$productPath = trim($item['thumbnail'] ?? '');

if (!empty($productPath)) {

    if (
        str_starts_with($productPath, 'http://') ||
        str_starts_with($productPath, 'https://')
    ) {
        $thumbnail = $productPath;
    } else {
        $thumbnail = base_url(ltrim($productPath, '/'));
    }

} else {
    $thumbnail = $placeholder;
}

?>

<div class="pcm-card" data-product-id="<?= $productId ?>">

    <?php if ($hasSale): ?>
        <div class="pcm-ribbon-discount">
            -<?= $discountPercent ?>%
        </div>
    <?php endif; ?>


    <!-- Hình ảnh sản phẩm -->
    <a
        class="pcm-card-thumb"
        href="<?= htmlspecialchars($productUrl, ENT_QUOTES, 'UTF-8') ?>"
        title="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>"
    >
        <img
            src="<?= htmlspecialchars($thumbnail, ENT_QUOTES, 'UTF-8') ?>"
            alt="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>"
            loading="lazy"
            width="180"
            height="180"
            onerror="this.onerror=null; this.src='<?= htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') ?>';"
        >
    </a>


    <!-- Nội dung sản phẩm -->
    <div class="pcm-card-content">

        <h3 class="pcm-card-title">
            <a
                href="<?= htmlspecialchars($productUrl, ENT_QUOTES, 'UTF-8') ?>"
                title="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>"
            >
                <?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>
            </a>
        </h3>


        <!-- Giá sản phẩm và giỏ hàng -->
        <div class="pcm-card-bottom">

            <div class="pcm-price-box">

                <span class="pcm-price-current">
                    <?= number_format($currentPrice, 0, ',', '.') ?> VNĐ
                </span>

                <?php if ($hasSale): ?>
                    <span class="pcm-price-old">
                        <?= number_format($regPrice, 0, ',', '.') ?> VNĐ
                    </span>
                <?php endif; ?>

            </div>


            <button
                type="button"
                class="pcm-cart-btn"
                data-pcm-add-cart="<?= $productId ?>"
                data-name="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>"
                aria-label="Thêm vào giỏ hàng"
            >
                <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
            </button>

        </div>

    </div>

</div>
