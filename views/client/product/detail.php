<?php
require VIEWS_DIR . '/layouts/client_header.php';
$details = json_decode($product['specifications'] ?? '{}', true) ?: [];
$specLabels = ['cpu' => 'Vi xử lý', 'mainboard' => 'Bo mạch chủ', 'ram' => 'Bộ nhớ RAM', 'vga' => 'Card đồ họa', 'storage' => 'Lưu trữ', 'psu' => 'Nguồn', 'case' => 'Vỏ máy', 'cooling' => 'Tản nhiệt', 'socket' => 'Socket', 'cores' => 'Số nhân', 'threads' => 'Số luồng', 'base_clock' => 'Xung nhịp cơ bản', 'boost_clock' => 'Xung nhịp tối đa', 'tdp' => 'Công suất', 'capacity' => 'Dung lượng', 'speed' => 'Tốc độ', 'interface' => 'Chuẩn kết nối', 'vram' => 'Bộ nhớ đồ họa', 'bus' => 'Bus bộ nhớ', 'type' => 'Loại bộ nhớ'];
$regularPrice = (float)$product['price'];
$salePrice = (float)($product['discount_price'] ?? 0);
$hasDiscount = $salePrice > 0 && $salePrice < $regularPrice;
?>
<div class="page-width">
    <nav class="breadcrumb" aria-label="Đường dẫn"><a href="<?= base_url() ?>">Trang chủ</a><span>/</span><a href="<?= base_url('san-pham') ?>">Cửa hàng</a><span>/</span><span><?= htmlspecialchars($product['brand_name'] ?? 'Sản phẩm') ?></span></nav>
    <article class="detail-layout"><figure class="detail-photo"><img src="<?= htmlspecialchars(product_image_url($product['thumbnail'] ?? '')) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="800" height="800" data-product-image onerror="this.onerror=null; this.src='<?= htmlspecialchars(asset('images/product-placeholder.svg'), ENT_QUOTES, 'UTF-8') ?>';"><figcaption class="image-caption">Ảnh sản phẩm minh họa</figcaption></figure><div class="detail-copy"><span class="eyebrow"><?= htmlspecialchars($product['brand_name'] ?? 'PCMarket') ?> / <?= htmlspecialchars($product['sku'] ?? '') ?></span><h1><?= htmlspecialchars($product['name']) ?></h1><div class="product-prices"><strong><?= format_money($hasDiscount ? $salePrice : $regularPrice) ?></strong><?php if ($hasDiscount): ?><s><?= format_money($regularPrice) ?></s><?php endif; ?></div><p><?= htmlspecialchars(strip_tags($product['summary'] ?? '')) ?></p><div class="detail-stock"><?= (int)$product['quantity'] > 0 ? 'Còn hàng · ' . (int)$product['quantity'] . ' sản phẩm' : 'Tạm hết hàng' ?></div><div class="detail-actions" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin:14px 0;"><?php if ((int)$product['quantity'] > 0): ?><button type="button" class="button button-primary" data-pcm-add-cart="<?= (int)$product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>"><i class="fa-solid fa-cart-plus" aria-hidden="true"></i> Thêm vào giỏ hàng</button><?php else: ?><button type="button" class="button button-outline" disabled>Tạm hết hàng</button><?php endif; ?><a class="button button-outline" href="#specifications">Xem thông số kỹ thuật <span aria-hidden="true">↓</span></a><?php $compareProduct = $product; require VIEWS_DIR . '/client/partials/pc_compare_button.php'; ?></div></div></article>
    <section class="detail-specs" id="specifications"><div><span class="eyebrow">CHI TIẾT CẤU HÌNH</span><h2>Thông số kỹ thuật</h2></div><?php if ($details): ?><table class="spec-table"><caption hidden>Thông số <?= htmlspecialchars($product['name']) ?></caption><tbody><?php foreach ($details as $key => $value): ?><tr><th scope="row"><?= htmlspecialchars($specLabels[$key] ?? ucfirst(str_replace('_', ' ', $key))) ?></th><td><?= htmlspecialchars(is_scalar($value) ? (string)$value : json_encode($value, JSON_UNESCAPED_UNICODE)) ?></td></tr><?php endforeach; ?></tbody></table><?php else: ?><p>Thông số chi tiết đang được cập nhật.</p><?php endif; ?></section>

    <?php
    $reviewList = $reviews ?? [];
    $reviewCount = count($reviewList);
    $avgRating = $reviewCount > 0 ? round(array_sum(array_column($reviewList, 'rating')) / $reviewCount, 1) : 0;
    ?>
    <section class="detail-specs" id="reviews" style="margin-top: 28px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:12px;margin-bottom:18px;">
            <div>
                <span class="eyebrow">NHẬN XÉT KHÁCH HÀNG</span>
                <h2>Đánh giá sản phẩm (<?= $reviewCount ?>)</h2>
            </div>
            <?php if ($reviewCount > 0): ?>
                <div style="font-size:15px;font-weight:700;color:#f59e0b;">
                    <?= str_repeat('★', (int)round($avgRating)) ?><?= str_repeat('☆', 5 - (int)round($avgRating)) ?>
                    <span style="color:#0f172a;margin-left:6px;"><?= number_format($avgRating, 1) ?> / 5.0</span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (is_logged_in()): ?>
            <form id="product-review-form" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:18px;margin-bottom:22px;">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
                    <label for="review-rating" style="font-size:13.5px;font-weight:700;color:#334155;">Đánh giá của bạn:</label>
                    <select id="review-rating" name="rating" style="padding:6px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:13.5px;color:#f59e0b;font-weight:700;">
                        <option value="5">★★★★★ — Tuyệt vời (5 sao)</option>
                        <option value="4">★★★★☆ — Hài lòng (4 sao)</option>
                        <option value="3">★★★☆☆ — Bình thường (3 sao)</option>
                        <option value="2">★★☆☆☆ — Chưa tốt (2 sao)</option>
                        <option value="1">★☆☆☆☆ — Rất tệ (1 sao)</option>
                    </select>
                </div>
                <textarea id="review-comment" name="comment" rows="3" required minlength="3" maxlength="1000" placeholder="Chia sẻ cảm nhận của bạn về hiệu năng, thiết kế, độ ồn của sản phẩm..." style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;resize:vertical;margin-bottom:10px;"></textarea>
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <button type="submit" class="button button-primary" id="review-submit-btn">Gửi đánh giá</button>
                    <span id="review-feedback" role="status" aria-live="polite" style="font-size:13px;font-weight:600;"></span>
                </div>
            </form>
        <?php else: ?>
            <p style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;margin-bottom:20px;font-size:14px;color:#475569;">
                Vui lòng <a href="<?= base_url('dang-nhap') ?>" style="color:#0284c7;font-weight:700;text-decoration:underline;">đăng nhập</a> để gửi đánh giá cho sản phẩm này.
            </p>
        <?php endif; ?>

        <div id="product-reviews-list" style="display:flex;flex-direction:column;gap:14px;">
            <?php if (empty($reviewList)): ?>
                <p id="no-reviews-msg" style="color:#64748b;font-size:14px;">Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên chia sẻ trải nghiệm!</p>
            <?php else: ?>
                <?php foreach ($reviewList as $rev): ?>
                    <article style="border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;background:#fff;">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                            <strong style="font-size:14px;color:#0f172a;"><?= htmlspecialchars($rev['full_name'] ?? 'Khách hàng') ?></strong>
                            <span style="font-size:12px;color:#64748b;"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></span>
                        </div>
                        <div style="color:#f59e0b;font-size:14px;margin-bottom:6px;"><?= str_repeat('★', (int)$rev['rating']) ?><?= str_repeat('☆', 5 - (int)$rev['rating']) ?></div>
                        <p style="margin:0;font-size:14px;color:#334155;line-height:1.5;"><?= nl2br(htmlspecialchars(html_entity_decode((string)$rev['comment'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8')) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php if (is_logged_in()): ?>
<script>
(() => {
    const form = document.getElementById('product-review-form');
    if (!form) return;
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('review-submit-btn');
        const fb = document.getElementById('review-feedback');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const base = document.body.dataset.appBase || '';
        const fd = new FormData(form);
        fd.append('_csrf', csrf);
        btn.disabled = true;
        fb.textContent = 'Đang gửi...';
        fb.style.color = '#475569';
        try {
            const res = await fetch(`${base}/api/reviews/store`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                body: new URLSearchParams(fd)
            });
            const data = await res.json();
            if (!res.ok || !data.success) throw new Error(data.message || 'Không thể gửi đánh giá.');
            fb.textContent = data.message;
            fb.style.color = '#15803d';
            document.getElementById('no-reviews-msg')?.remove();
            const list = document.getElementById('product-reviews-list');
            const stars = '★'.repeat(data.review.rating) + '☆'.repeat(5 - data.review.rating);
            const item = document.createElement('article');
            item.style.cssText = 'border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;background:#fff;';
            item.innerHTML = `<div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;"><strong style="font-size:14px;color:#0f172a;"></strong><span style="font-size:12px;color:#64748b;"></span></div><div style="color:#f59e0b;font-size:14px;margin-bottom:6px;">${stars}</div><p style="margin:0;font-size:14px;color:#334155;line-height:1.5;"></p>`;
            item.querySelector('strong').textContent = data.review.full_name;
            item.querySelector('span').textContent = data.review.created_at;
            item.querySelector('p').textContent = document.getElementById('review-comment').value.trim();
            list.prepend(item);
            form.reset();
        } catch (err) {
            fb.textContent = err.message;
            fb.style.color = '#dc2626';
        } finally {
            btn.disabled = false;
        }
    });
})();
</script>
<?php endif; ?>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
