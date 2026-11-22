<?php if (in_array((int)($compareProduct['category_id'] ?? 0), [1, 2, 3, 15, 16, 17, 19], true)): ?>
<?php
$comparisonPrice = (float)$compareProduct['price'];
$comparisonDiscount = (float)($compareProduct['discount_price'] ?? 0);
$comparisonData = [
    'id' => (int)$compareProduct['id'], 'name' => $compareProduct['name'],
    'category' => (int)$compareProduct['category_id'], 'quantity' => (int)($compareProduct['quantity'] ?? 0),
    'price' => $comparisonDiscount > 0 && $comparisonDiscount < $comparisonPrice ? $comparisonDiscount : $comparisonPrice,
    'image' => product_image_url($compareProduct['thumbnail'] ?? ''), 'url' => base_url('san-pham/' . rawurlencode($compareProduct['slug'])),
    'specs' => json_decode($compareProduct['specifications'] ?? '{}', true) ?: [],
];
$btnClass = !empty($compareButtonClass) ? $compareButtonClass : 'pc-compare-toggle';
$btnStyle = !empty($compareButtonStyle) ? $compareButtonStyle : '';
?>
<button type="button" class="<?= htmlspecialchars($btnClass) ?>" data-pc-compare="<?= htmlspecialchars(json_encode($comparisonData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>" aria-pressed="false" <?= $btnStyle ? 'style="' . htmlspecialchars($btnStyle, ENT_QUOTES, 'UTF-8') . '"' : '' ?> hidden><i class="fa-solid fa-code-compare" aria-hidden="true"></i><span>So sánh PC</span></button>
<?php unset($compareButtonClass, $compareButtonStyle); ?>
<?php endif; ?>
