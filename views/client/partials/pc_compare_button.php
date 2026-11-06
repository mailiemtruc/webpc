<?php if ((int)($compareProduct['category_id'] ?? 0) >= 1 && (int)$compareProduct['category_id'] <= 3): ?>
<?php
$comparisonPrice = (float)$compareProduct['price'];
$comparisonDiscount = (float)($compareProduct['discount_price'] ?? 0);
$comparisonData = [
    'id' => (int)$compareProduct['id'], 'name' => $compareProduct['name'],
    'category' => (int)$compareProduct['category_id'], 'quantity' => (int)($compareProduct['quantity'] ?? 0),
    'price' => $comparisonDiscount > 0 && $comparisonDiscount < $comparisonPrice ? $comparisonDiscount : $comparisonPrice,
    'image' => $compareProduct['thumbnail'], 'url' => base_url('san-pham/' . rawurlencode($compareProduct['slug'])),
    'specs' => json_decode($compareProduct['specifications'] ?? '{}', true) ?: [],
];
?>
<button type="button" class="pc-compare-toggle" data-pc-compare="<?= htmlspecialchars(json_encode($comparisonData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>" aria-pressed="false" hidden><i class="fa-solid fa-code-compare" aria-hidden="true"></i><span>So sánh PC</span></button>
<?php endif; ?>
