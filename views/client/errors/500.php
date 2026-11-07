<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>
<section class="page-width error-page">
    <div class="error-number" aria-hidden="true">500</div>
    <span class="eyebrow">SỰ CỐ MÁY CHỦ</span>
    <h1>ĐÃ CÓ LỖI XẢY RA TRÊN HỆ THỐNG</h1>
    <p>Máy chủ gặp sự cố trong quá trình xử lý yêu cầu của bạn.<br>Đội ngũ kỹ thuật đã được thông báo. Vui lòng tải lại trang sau ít phút.</p>
    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 24px;">
        <a class="button button-primary" href="javascript:location.reload()">Tải lại trang <span aria-hidden="true">↻</span></a>
        <a class="button button-outline" href="<?= base_url() ?>">Về trang chủ</a>
    </div>
</section>
<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>

