<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>

<div class="page-width" style="padding: 30px 0 60px;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Đường dẫn" style="margin-bottom: 24px;">
        <a href="<?= base_url() ?>">Trang chủ</a><span>/</span><a href="<?= base_url('tin-tuc') ?>">Tin tức</a><span>/</span><span><?= htmlspecialchars($blog['title']) ?></span>
    </nav>

    <article style="max-width: 860px; margin: 0 auto; background: #fff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 36px 32px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
        <header style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9;">
            <div style="display: flex; gap: 14px; font-size: 13px; color: #64748b; margin-bottom: 12px; flex-wrap: wrap;">
                <span><i class="fa-regular fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($blog['created_at'])) ?></span>
                <span><i class="fa-regular fa-user"></i> Tác giả: <strong><?= htmlspecialchars($blog['author_name'] ?? 'Ban biên tập PCMarket') ?></strong></span>
                <span><i class="fa-regular fa-eye"></i> <?= number_format((int)$blog['view_count']) ?> lượt xem</span>
            </div>
            <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1.35; margin-bottom: 16px;">
                <?= htmlspecialchars($blog['title']) ?>
            </h1>
            <?php if (!empty($blog['summary'])): ?>
                <p style="font-size: 16px; font-weight: 500; color: #475569; line-height: 1.6; background: #f8fafc; padding: 14px 18px; border-left: 4px solid #0284c7; border-radius: 4px; margin: 0;">
                    <?= htmlspecialchars($blog['summary']) ?>
                </p>
            <?php endif; ?>
        </header>

        <!-- Ảnh bài viết -->
        <?php if (!empty($blog['thumbnail'])): ?>
            <figure style="margin: 0 0 28px; border-radius: 8px; overflow: hidden; max-height: 480px; background: #f1f5f9;">
                <img src="<?= htmlspecialchars(product_image_url($blog['thumbnail'])) ?>" 
                     alt="<?= htmlspecialchars($blog['title']) ?>" 
                     style="width: 100%; height: auto; max-height: 480px; object-fit: cover;"
                     onerror="this.onerror=null; this.src='<?= asset('images/product-placeholder.svg') ?>';">
            </figure>
        <?php endif; ?>

        <!-- Nội dung bài viết -->
        <div style="font-size: 16px; line-height: 1.8; color: #1e293b;" class="blog-content">
            <?= $blog['content'] /* Sanitized by AdminBlogController sanitizeContent() */ ?>
        </div>

        <footer style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <a href="<?= base_url('tin-tuc') ?>" style="display: inline-flex; align-items: center; gap: 8px; color: #0284c7; font-weight: 700; text-decoration: none;">
                <span aria-hidden="true">←</span> Quay lại danh sách tin tức
            </a>
            <div style="display: flex; gap: 10px;">
                <a href="<?= base_url('san-pham') ?>" class="button button-primary" style="padding: 8px 18px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 700; background: #0284c7; color: #fff;">
                    XEM SẢN PHẨM PC ↗
                </a>
            </div>
        </footer>
    </article>
</div>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
