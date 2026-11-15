<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>

<div class="page-width" style="padding: 30px 0 60px;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Đường dẫn" style="margin-bottom: 20px;">
        <a href="<?= base_url() ?>">Trang chủ</a><span>/</span><span>Tin tức công nghệ</span>
    </nav>

    <div style="margin-bottom: 28px; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 12px;">
        <div>
            <span style="font-size: 12px; font-weight: 800; color: #0284c7; text-transform: uppercase; letter-spacing: 1px;">BẢN TIN CÔNG NGHỆ & THỦ THUẬT</span>
            <h1 style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 4px;">TIN TỨC PCMARKET</h1>
        </div>
        <span style="font-size: 14px; color: #64748b;"><?= count($blogs) ?> bài viết</span>
    </div>

    <?php if (empty($blogs)): ?>
        <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center; padding: 60px 20px;">
            <i class="fa-solid fa-newspaper" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
            <h2 style="font-size: 18px; font-weight: 700; color: #334155; margin-bottom: 8px;">Chưa có bài viết nào</h2>
            <p style="color: #64748b; font-size: 14px;">Các bài viết công nghệ mới nhất đang được biên soạn.</p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
            <?php foreach ($blogs as $blog): ?>
                <?php
                $thumb = product_image_url($blog['thumbnail'] ?? '');
                $blogUrl = base_url('tin-tuc/' . rawurlencode($blog['slug']));
                ?>
                <article style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <a href="<?= $blogUrl ?>" style="display: block; aspect-ratio: 16/9; overflow: hidden; background: #f8fafc;">
                        <img src="<?= htmlspecialchars($thumb) ?>" 
                             alt="<?= htmlspecialchars($blog['title']) ?>" 
                             loading="lazy" 
                             style="width: 100%; height: 100%; object-fit: cover;"
                             onerror="this.onerror=null; this.src='<?= asset('images/product-placeholder.svg') ?>';">
                    </a>
                    <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                        <div style="display: flex; gap: 12px; font-size: 12px; color: #64748b; margin-bottom: 10px;">
                            <span><i class="fa-regular fa-calendar"></i> <?= date('d/m/Y', strtotime($blog['created_at'])) ?></span>
                            <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars($blog['author_name'] ?? 'Ban biên tập') ?></span>
                            <span><i class="fa-regular fa-eye"></i> <?= number_format((int)($blog['view_count'] ?? 0)) ?> lượt xem</span>
                        </div>
                        <h2 style="font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 10px; line-height: 1.4;">
                            <a href="<?= $blogUrl ?>" style="color: inherit; text-decoration: none;">
                                <?= htmlspecialchars($blog['title']) ?>
                            </a>
                        </h2>
                        <?php if (!empty($blog['summary'])): ?>
                            <p style="font-size: 13.5px; color: #475569; line-height: 1.5; margin-bottom: 16px; flex: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($blog['summary']) ?>
                            </p>
                        <?php endif; ?>
                        <div style="margin-top: auto;">
                            <a href="<?= $blogUrl ?>" style="font-size: 13px; font-weight: 700; color: #0284c7; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                Đọc bài viết <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
