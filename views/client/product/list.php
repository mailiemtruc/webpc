<?php
require VIEWS_DIR . '/layouts/client_header.php';
$searchQuery = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$categorySlug = is_string($_GET['danh-muc'] ?? null) ? $_GET['danh-muc'] : '';
$sort = is_string($_GET['sort'] ?? null) ? $_GET['sort'] : 'newest';
$categoryId = $catalogCategories[$categorySlug][1] ?? null;
$visibleProducts = array_values(array_filter($products, static function ($product) use ($searchQuery, $categorySlug, $categoryId) {
    if ((int)($product['status'] ?? 0) !== 1) return false;
    if ($searchQuery !== '' && mb_stripos($product['name'] . ' ' . ($product['sku'] ?? ''), $searchQuery, 0, 'UTF-8') === false) return false;
    if ($categorySlug === 'linh-kien-may-tinh') return (int)$product['category_id'] >= 4 && (int)$product['category_id'] <= 12;
    return !$categoryId || (int)$product['category_id'] === $categoryId;
}));
if (in_array($sort, ['price-asc', 'price-desc'], true)) {
    usort($visibleProducts, static function ($a, $b) use ($sort) {
        $aPrice = (float)(!empty($a['discount_price']) ? $a['discount_price'] : $a['price']);
        $bPrice = (float)(!empty($b['discount_price']) ? $b['discount_price'] : $b['price']);
        return $sort === 'price-asc' ? $aPrice <=> $bPrice : $bPrice <=> $aPrice;
    });
}
$catalogLabel = $categorySlug === 'linh-kien-may-tinh' ? 'Linh kiện máy tính' : ($catalogCategories[$categorySlug][0] ?? 'Tất cả sản phẩm');
?>
<div class="page-width">
    <nav class="breadcrumb" aria-label="Đường dẫn"><a href="<?= base_url() ?>">Trang chủ</a><span>/</span><span>Cửa hàng</span></nav>
    <div class="catalog-heading"><div><span class="eyebrow">Tìm điều phù hợp với bạn</span><h1><?= htmlspecialchars($catalogLabel) ?></h1><p><?= $searchQuery !== '' ? 'Kết quả cho “' . htmlspecialchars($searchQuery) . '”' : 'Máy tính và linh kiện cho công việc, học tập và những giờ thư giãn.' ?></p></div></div>
    <div class="catalog-layout">
        <nav class="catalog-sidebar" aria-label="Lọc danh mục"><h2>Danh mục sản phẩm</h2><a href="<?= base_url('san-pham') ?>" <?= $categorySlug === '' ? 'aria-current="page"' : '' ?>>Tất cả sản phẩm</a><?php foreach ($catalogCategories as $slug => $category): ?><a href="<?= base_url('san-pham?danh-muc=' . $slug) ?>" <?= $categorySlug === $slug ? 'aria-current="page"' : '' ?>><?= $category[0] ?></a><?php endforeach; ?><a href="<?= base_url('san-pham?danh-muc=linh-kien-may-tinh') ?>" <?= $categorySlug === 'linh-kien-may-tinh' ? 'aria-current="page"' : '' ?>>Tất cả linh kiện</a></nav>
        <section aria-label="Kết quả sản phẩm"><div class="catalog-toolbar"><span><?= count($visibleProducts) ?> sản phẩm</span><form class="sort-form" method="get" action="<?= base_url('san-pham') ?>"><?php if ($categorySlug): ?><input type="hidden" name="danh-muc" value="<?= htmlspecialchars($categorySlug) ?>"><?php endif; ?><?php if ($searchQuery): ?><input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>"><?php endif; ?><label for="catalog-sort">Sắp xếp</label><select id="catalog-sort" name="sort"><option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option><option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Giá: thấp đến cao</option><option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Giá: cao đến thấp</option></select><button type="submit">Áp dụng</button></form></div><div class="product-grid catalog-grid"><?php foreach ($visibleProducts as $item) { require VIEWS_DIR . '/client/partials/product_card.php'; } ?><?php if (!$visibleProducts): ?><div class="empty-state"><h2>Chưa tìm thấy sản phẩm phù hợp.</h2><p>Thử một tên ngắn hơn hoặc xem các danh mục khác.</p><a class="button button-outline" href="<?= base_url('san-pham') ?>">Xem tất cả sản phẩm ↗</a></div><?php endif; ?></div></section>
    </div>
</div>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
