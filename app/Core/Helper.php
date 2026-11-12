<?php
/**
 * Global Helper Functions for WebPC
 * Môn: Lập trình Web - UTH
 */

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        static $envData = null;
        if ($envData === null) {
            $envData = [];
            $envPath = dirname(__DIR__, 2) . '/.env';
            if (file_exists($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                        continue;
                    }
                    [$k, $v] = explode('=', $line, 2);
                    $envData[trim($k)] = trim(trim($v), '"\'');
                }
            }
        }
        return $envData[$key] ?? getenv($key) ?: $default;
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string {
        $appUrl = rtrim(env('APP_URL', 'http://localhost/webpc/public'), '/');
        $path = ltrim($path, '/');
        return $path !== '' ? "{$appUrl}/{$path}" : $appUrl;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('upload_url')) {
    function upload_url(string $path = ''): string {
        if (empty($path)) {
            return asset('images/product-placeholder.svg');
        }
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return $path;
        }
        return base_url('uploads/' . ltrim($path, '/'));
    }
}

if (!function_exists('product_image_url')) {
    /**
     * Chuẩn hóa đường dẫn ảnh sản phẩm (hỗ trợ URL ngoài, assets/..., uploads/... và file upload từ Admin)
     */
    function product_image_url(?string $path = ''): string {
        $clean = ltrim(trim((string)$path), '/');
        if ($clean === '') {
            return asset('images/product-placeholder.svg');
        }
        if (strpos($clean, 'http://') === 0 || strpos($clean, 'https://') === 0) {
            return $clean;
        }
        if (strpos($clean, 'assets/') === 0 || strpos($clean, 'uploads/') === 0) {
            return base_url($clean);
        }
        if (
            strpos($clean, 'products/') === 0 ||
            strpos($clean, 'banners/') === 0 ||
            strpos($clean, 'blogs/') === 0 ||
            strpos($clean, 'avatars/') === 0
        ) {
            return base_url('uploads/' . $clean);
        }
        return asset($clean);
    }
}

if (!function_exists('format_money')) {
    function format_money($amount): string {
        if ($amount === null || $amount === '') {
            return 'Liên hệ';
        }
        return number_format((float)$amount, 0, ',', '.') . ' ₫';
    }
}

if (!function_exists('slugify')) {
    function slugify(string $str): string {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', '-', $str);
        return trim($str, '-');
    }
}

if (!function_exists('view')) {
    function view(string $viewPath, array $data = []): void {
        extract($data);
        $viewFile = dirname(__DIR__, 2) . '/views/' . str_replace('.', '/', $viewPath) . '.php';
        if (!file_exists($viewFile)) {
            die("View not found: {$viewPath} (File: {$viewFile})");
        }
        require $viewFile;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void {
        $url = (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) 
            ? $path 
            : base_url($path);
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('json_response')) {
    function json_response(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

if (!function_exists('set_flash')) {
    function set_flash(string $type, string $message): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$type] = $message;
    }
}

if (!function_exists('get_flash')) {
    function get_flash(string $type): ?string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['flash'][$type])) {
            $msg = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $msg;
        }
        return null;
    }
}

if (!function_exists('auth_user')) {
    function auth_user() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool {
        return auth_user() !== null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool {
        $user = auth_user();
        return $user && ($user['role'] === 'admin');
    }
}

if (!function_exists('is_staff')) {
    function is_staff(): bool {
        $user = auth_user();
        return $user && ($user['role'] === 'admin' || $user['role'] === 'staff');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
    }
}

if (!function_exists('upload_file')) {
    /**
     * Upload tệp hình ảnh an toàn vào thư mục public/uploads/{folder}/
     * Chống tải lên mã độc shell, kiểm tra MIME type thực, sinh tên ngẫu nhiên chống trùng file.
     *
     * @param array $file Mảng $_FILES['input_name']
     * @param string $folder Thư mục con đích (products, banners, blogs, avatars)
     * @param array $allowedExtensions Các đuôi mở rộng hợp lệ
     * @param int $maxSize Dung lượng tối đa (mặc định 5MB = 5242880 bytes)
     * @return string|null Đường dẫn tương đối để lưu vào DB (ví dụ: 'products/abc12345.webp') hoặc null nếu lỗi
     */
    function upload_file(
        array $file,
        string $folder = 'products',
        array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'],
        int $maxSize = 5242880
    ): ?string {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] <= 0 || $file['size'] > $maxSize) {
            return null;
        }

        $origName = $file['name'] ?? '';
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions, true)) {
            return null;
        }

        // Kiểm tra MIME type thực tế bằng Fileinfo
        $tmpPath = $file['tmp_name'];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);

            $allowedMimes = [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/svg+xml',
                'image/x-icon',
            ];

            if (!in_array($mime, $allowedMimes, true)) {
                return null;
            }
        }

        // Chuẩn hóa thư mục đích an toàn
        $cleanFolder = trim(preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $folder), '/');
        $uploadBase = dirname(__DIR__, 2) . '/public/uploads/' . $cleanFolder;

        if (!is_dir($uploadBase)) {
            mkdir($uploadBase, 0755, true);
        }

        // Tên file ngẫu nhiên an toàn chống ghi đè
        $fileName = bin2hex(random_bytes(10)) . '_' . time() . '.' . $ext;
        $destination = $uploadBase . '/' . $fileName;

        if (move_uploaded_file($tmpPath, $destination)) {
            return $cleanFolder . '/' . $fileName;
        }

        return null;
    }
}

if (!function_exists('upload_multiple_files')) {
    /**
     * Upload nhiều tệp cùng lúc (dành cho gallery ảnh chi tiết sản phẩm)
     */
    function upload_multiple_files(array $filesInput, string $folder = 'products'): array {
        $uploaded = [];
        if (!isset($filesInput['name']) || !is_array($filesInput['name'])) {
            return $uploaded;
        }

        $count = count($filesInput['name']);
        for ($i = 0; $i < $count; $i++) {
            $singleFile = [
                'name'     => $filesInput['name'][$i],
                'type'     => $filesInput['type'][$i] ?? '',
                'tmp_name' => $filesInput['tmp_name'][$i],
                'error'    => $filesInput['error'][$i],
                'size'     => $filesInput['size'][$i],
            ];
            $path = upload_file($singleFile, $folder);
            if ($path) {
                $uploaded[] = $path;
            }
        }
        return $uploaded;
    }
}

if (!function_exists('delete_uploaded_file')) {
    /**
     * Xóa tệp ảnh cũ khi cập nhật để dọn dẹp dung lượng lưu trữ
     */
    function delete_uploaded_file(?string $relativePath): bool {
        if (empty($relativePath) || strpos($relativePath, '..') !== false) {
            return false;
        }
        $fullPath = dirname(__DIR__, 2) . '/public/uploads/' . ltrim($relativePath, '/');
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}

if (!function_exists('paginate')) {
    /**
     * Tính toán thông số phân trang chuẩn
     *
     * @param int $totalItems Tổng số bản ghi trong CSDL
     * @param int $perPage Số bản ghi mỗi trang (mặc định 12)
     * @param int $currentPage Trang hiện tại (mặc định 1)
     * @return array
     */
    function paginate(int $totalItems, int $perPage = 12, int $currentPage = 1): array {
        $perPage = max(1, $perPage);
        $totalPages = max(1, (int)ceil($totalItems / $perPage));
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $perPage;

        return [
            'total_items'  => $totalItems,
            'per_page'     => $perPage,
            'current_page' => $currentPage,
            'total_pages'  => $totalPages,
            'offset'       => $offset,
            'limit'        => $perPage,
            'has_prev'     => $currentPage > 1,
            'has_next'     => $currentPage < $totalPages,
            'prev_page'    => max(1, $currentPage - 1),
            'next_page'    => min($totalPages, $currentPage + 1),
        ];
    }
}

if (!function_exists('render_pagination')) {
    /**
     * Tạo mã HTML thanh phân trang đẹp mắt, đồng bộ giao diện storefront
     */
    function render_pagination(array $p, string $baseUrl, array $params = []): string {
        if ($p['total_pages'] <= 1) {
            return '';
        }

        $buildUrl = function(int $page) use ($baseUrl, $params) {
            $query = array_merge($params, ['trang' => $page]);
            $qs = http_build_query($query);
            return $baseUrl . ($qs ? '?' . $qs : '');
        };

        $html = '<nav class="pagination" aria-label="Phân trang danh sách">';

        // Nút Trang trước
        if ($p['has_prev']) {
            $html .= '<a class="pagination-prev" href="' . htmlspecialchars($buildUrl($p['prev_page'])) . '" aria-label="Trang trước">‹</a>';
        }

        $current = $p['current_page'];
        $total = $p['total_pages'];
        $start = max(1, $current - 2);
        $end = min($total, $current + 2);

        if ($start > 1) {
            $html .= '<a href="' . htmlspecialchars($buildUrl(1)) . '">1</a>';
            if ($start > 2) {
                $html .= '<span style="display:grid;place-items:center;padding:0 4px;" aria-hidden="true">…</span>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i === $current) {
                $html .= '<a href="' . htmlspecialchars($buildUrl($i)) . '" aria-current="page">' . $i . '</a>';
            } else {
                $html .= '<a href="' . htmlspecialchars($buildUrl($i)) . '">' . $i . '</a>';
            }
        }

        if ($end < $total) {
            if ($end < $total - 1) {
                $html .= '<span style="display:grid;place-items:center;padding:0 4px;" aria-hidden="true">…</span>';
            }
            $html .= '<a href="' . htmlspecialchars($buildUrl($total)) . '">' . $total . '</a>';
        }

        // Nút Trang sau
        if ($p['has_next']) {
            $html .= '<a class="pagination-next" href="' . htmlspecialchars($buildUrl($p['next_page'])) . '" aria-label="Trang sau">›</a>';
        }

        $html .= '</nav>';

        return $html;
    }
}

