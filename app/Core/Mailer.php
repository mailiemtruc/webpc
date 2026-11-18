<?php
namespace App\Core;

/**
 * Trình gửi email hệ thống (hỗ trợ SMTP Gmail STARTTLS/SSL, PHP mail() và log nội bộ local).
 */
class Mailer {

    /**
     * Gửi email xác nhận địa chỉ email khi đăng ký tài khoản mới.
     *
     * @return array{sent: bool, driver: string, error: ?string}
     */
    public static function sendAccountVerification(string $toEmail, string $toName, string $verifyLink): array {
        $appName  = env('APP_NAME', 'PCMarket');
        $subject  = "[{$appName}] Xác nhận tài khoản email của bạn";
        $safeName = htmlspecialchars($toName !== '' ? $toName : 'Quý khách', ENT_QUOTES, 'UTF-8');
        $safeUrl  = htmlspecialchars($verifyLink, ENT_QUOTES, 'UTF-8');

        $body = <<<HTML
<h2 style="margin:0 0 12px; color:#1d4ed8; font-size:20px;">Xác nhận địa chỉ email</h2>
<p style="margin:0 0 14px; font-size:14px; line-height:1.6;">Xin chào <strong>{$safeName}</strong>,</p>
<p style="margin:0 0 18px; font-size:14px; line-height:1.6;">
  Cảm ơn bạn đã đăng ký tài khoản tại <strong>PCMarket</strong>. Vui lòng nhấn nút bên dưới để kích hoạt tài khoản
  (liên kết có hiệu lực trong <strong>24 giờ</strong>):
</p>
<p style="margin:0 0 22px; text-align:center;">
  <a href="{$safeUrl}" style="display:inline-block; background:#2454d6; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:6px; font-weight:700; font-size:14px;">
    Xác nhận tài khoản
  </a>
</p>
<p style="margin:0 0 10px; font-size:12.5px; color:#64748b; line-height:1.5; word-break:break-all;">
  Nếu nút không hoạt động, hãy sao chép liên kết sau vào trình duyệt:<br>{$safeUrl}
</p>
HTML;

        return self::send(
            $toEmail,
            $subject,
            self::wrapHtmlTemplate($body, 'Nếu bạn không thực hiện đăng ký tại PCMarket, vui lòng bỏ qua email này.')
        );
    }

    /**
     * Gửi email đặt lại mật khẩu.
     *
     * @return array{sent: bool, driver: string, error: ?string}
     */
    public static function sendPasswordReset(string $toEmail, string $toName, string $resetLink): array {
        $appName  = env('APP_NAME', 'PCMarket');
        $subject  = "[{$appName}] Hướng dẫn đặt lại mật khẩu tài khoản";
        $safeName = htmlspecialchars($toName !== '' ? $toName : 'Quý khách', ENT_QUOTES, 'UTF-8');
        $safeUrl  = htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8');

        $body = <<<HTML
<h2 style="margin:0 0 12px; color:#1d4ed8; font-size:20px;">Yêu cầu đặt lại mật khẩu</h2>
<p style="margin:0 0 14px; font-size:14px; line-height:1.6;">Xin chào <strong>{$safeName}</strong>,</p>
<p style="margin:0 0 18px; font-size:14px; line-height:1.6;">
  Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản PCMarket liên kết với email này.
  Vui lòng nhấn nút bên dưới để thiết lập mật khẩu mới (liên kết có hiệu lực trong <strong>30 phút</strong>):
</p>
<p style="margin:0 0 22px; text-align:center;">
  <a href="{$safeUrl}" style="display:inline-block; background:#2454d6; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:6px; font-weight:700; font-size:14px;">
    Đặt lại mật khẩu
  </a>
</p>
<p style="margin:0 0 10px; font-size:12.5px; color:#64748b; line-height:1.5; word-break:break-all;">
  Nếu nút không hoạt động, hãy sao chép liên kết sau vào trình duyệt:<br>{$safeUrl}
</p>
HTML;

        return self::send(
            $toEmail,
            $subject,
            self::wrapHtmlTemplate($body, 'Nếu bạn không thực hiện yêu cầu này, hãy bỏ qua email để giữ an toàn cho tài khoản.')
        );
    }

    /**
     * Gửi email cảnh báo bảo mật (đăng nhập mới, đổi mật khẩu, bật/tắt 2FA, đổi mã dự phòng).
     *
     * @param array{ip?:string, user_agent?:string, time?:string} $meta
     * @return array{sent: bool, driver: string, error: ?string}
     */
    public static function sendSecurityAlert(
        string $toEmail,
        string $toName,
        string $eventTitle,
        string $detailsText,
        array $meta = []
    ): array {
        $appName     = env('APP_NAME', 'PCMarket');
        $subject     = "[{$appName}] Cảnh báo bảo mật: {$eventTitle}";
        $safeName    = htmlspecialchars($toName !== '' ? $toName : 'Quý khách', ENT_QUOTES, 'UTF-8');
        $safeTitle   = htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8');
        $safeDetails = nl2br(htmlspecialchars($detailsText, ENT_QUOTES, 'UTF-8'));
        $time        = htmlspecialchars($meta['time'] ?? date('d/m/Y H:i:s'), ENT_QUOTES, 'UTF-8');
        $ip          = htmlspecialchars($meta['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'Không xác định'), ENT_QUOTES, 'UTF-8');
        $agent       = htmlspecialchars(mb_substr((string)($meta['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Trình duyệt web')), 0, 140), ENT_QUOTES, 'UTF-8');
        $securityUrl = htmlspecialchars(base_url('tai-khoan/bao-mat'), ENT_QUOTES, 'UTF-8');

        $body = <<<HTML
<h2 style="margin:0 0 12px; color:#1d4ed8; font-size:20px;">{$safeTitle}</h2>
<p style="margin:0 0 14px; font-size:14px; line-height:1.6;">Xin chào <strong>{$safeName}</strong>,</p>
<p style="margin:0 0 14px; font-size:14px; line-height:1.6;">{$safeDetails}</p>
<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px 14px; font-size:13px; line-height:1.6; margin:0 0 18px;">
  <div><strong>Thời gian:</strong> {$time}</div>
  <div><strong>Địa chỉ IP:</strong> {$ip}</div>
  <div><strong>Thiết bị / Trình duyệt:</strong> {$agent}</div>
</div>
<p style="margin:0 0 10px; font-size:13px; color:#475569; line-height:1.6;">
  Nếu đây chính là bạn, không cần thực hiện thêm thao tác nào. Nếu bạn không nhận ra hoạt động này, hãy đổi mật khẩu và kiểm tra ngay tại
  <a href="{$securityUrl}" style="color:#2454d6; font-weight:600;">Trung tâm bảo mật PCMarket</a>.
</p>
HTML;

        return self::send(
            $toEmail,
            $subject,
            self::wrapHtmlTemplate($body, 'Email tự động từ hệ thống bảo mật tài khoản PCMarket.')
        );
    }

    /**
     * Gửi email xác nhận đơn hàng sau khi đặt hàng thành công (COD hoặc VietQR).
     *
     * @param array<string,mixed> $order
     * @param array<int,array<string,mixed>> $items
     * @return array{sent: bool, driver: string, error: ?string}
     */
    public static function sendOrderConfirmation(array $order, array $items): array {
        $toEmail = trim((string)($order['customer_email'] ?? ''));
        if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return ['sent' => false, 'driver' => 'none', 'error' => 'Email không hợp lệ hoặc bị bỏ trống.'];
        }

        $appName       = env('APP_NAME', 'PCMarket');
        $orderCode     = (string)($order['order_code'] ?? '');
        $subject       = "[{$appName}] Xác nhận đơn hàng #{$orderCode}";
        $safeName      = htmlspecialchars((string)($order['customer_name'] ?? 'Quý khách'), ENT_QUOTES, 'UTF-8');
        $safePhone     = htmlspecialchars((string)($order['customer_phone'] ?? ''), ENT_QUOTES, 'UTF-8');
        $safeAddress   = htmlspecialchars((string)($order['shipping_address'] ?? ''), ENT_QUOTES, 'UTF-8');
        $safeCode      = htmlspecialchars($orderCode, ENT_QUOTES, 'UTF-8');
        $isBanking     = ($order['payment_method'] ?? 'cod') === 'banking';
        $paymentLabel  = $isBanking ? 'Chuyển khoản Ngân hàng (VietQR)' : 'Thanh toán khi nhận hàng (COD)';
        $subtotalText  = number_format((float)($order['subtotal'] ?? 0), 0, ',', '.') . ' VNĐ';
        $finalText     = number_format((float)($order['final_total'] ?? 0), 0, ',', '.') . ' VNĐ';
        $orderUrl      = htmlspecialchars(base_url('dat-hang-thanh-cong?code=' . rawurlencode($orderCode)), ENT_QUOTES, 'UTF-8');

        $rowsHtml = '';
        foreach ($items as $item) {
            $itemName  = htmlspecialchars((string)($item['name'] ?? $item['product_name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8');
            $itemQty   = (int)($item['quantity'] ?? 1);
            $itemTotal = (float)($item['line_total'] ?? $item['total'] ?? ((float)($item['price'] ?? 0) * $itemQty));
            $itemPrice = number_format($itemTotal, 0, ',', '.') . ' VNĐ';
            $rowsHtml .= "<tr>"
                . "<td style=\"padding:8px 10px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#1e293b;\">{$itemName} <strong>× {$itemQty}</strong></td>"
                . "<td style=\"padding:8px 10px; border-bottom:1px solid #e2e8f0; font-size:13px; text-align:right; font-weight:700; color:#dc2626; white-space:nowrap;\">{$itemPrice}</td>"
                . "</tr>";
        }

        $discountRow = '';
        $discountAmt = (float)($order['discount_amount'] ?? 0);
        if (!empty($order['coupon_code']) && $discountAmt > 0) {
            $safeCoupon = htmlspecialchars((string)$order['coupon_code'], ENT_QUOTES, 'UTF-8');
            $discText   = '-' . number_format($discountAmt, 0, ',', '.') . ' VNĐ';
            $discountRow = "<tr>"
                . "<td style=\"padding:8px 10px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#15803d;\">Mã giảm giá ({$safeCoupon})</td>"
                . "<td style=\"padding:8px 10px; border-bottom:1px solid #e2e8f0; font-size:13px; text-align:right; font-weight:700; color:#15803d; white-space:nowrap;\">{$discText}</td>"
                . "</tr>";
        }

        $bankingHtml = '';
        if ($isBanking) {
            $bankCfg    = payment_bank_config();
            $bankName   = htmlspecialchars($bankCfg['bank_name'], ENT_QUOTES, 'UTF-8');
            $accountNo  = htmlspecialchars($bankCfg['account_no'], ENT_QUOTES, 'UTF-8');
            $accountNm  = htmlspecialchars($bankCfg['account_name'], ENT_QUOTES, 'UTF-8');
            $qrUrl      = htmlspecialchars(vietqr_image_url((int)round((float)($order['final_total'] ?? 0)), $orderCode), ENT_QUOTES, 'UTF-8');
            $bankingHtml = <<<HTML
<div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:16px; margin:18px 0; font-size:13px; color:#1e3a8a; line-height:1.7;">
  <div style="font-weight:800; color:#1d4ed8; margin-bottom:8px;">THÔNG TIN CHUYỂN KHOẢN VIETQR</div>
  <div><strong>Ngân hàng:</strong> {$bankName}</div>
  <div><strong>Số tài khoản:</strong> <code>{$accountNo}</code></div>
  <div><strong>Chủ tài khoản:</strong> {$accountNm}</div>
  <div><strong>Số tiền:</strong> <strong style="color:#dc2626;">{$finalText}</strong></div>
  <div><strong>Nội dung chuyển khoản:</strong> <code>{$safeCode}</code></div>
  <div style="margin-top:12px; text-align:center;">
    <img src="{$qrUrl}" alt="Mã QR VietQR" width="180" height="180" style="border:1px solid #dbeafe; border-radius:6px; background:#fff; padding:6px;">
  </div>
</div>
HTML;
        }

        $body = <<<HTML
<h2 style="margin:0 0 12px; color:#15803d; font-size:20px;">Đặt hàng thành công!</h2>
<p style="margin:0 0 14px; font-size:14px; line-height:1.6;">Xin chào <strong>{$safeName}</strong>,</p>
<p style="margin:0 0 16px; font-size:14px; line-height:1.6;">Cảm ơn bạn đã đặt hàng tại <strong>PCMarket</strong>. Mã đơn hàng của bạn là <strong style="color:#0284c7;">{$safeCode}</strong>.</p>
<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px 14px; font-size:13px; line-height:1.7; margin:0 0 16px;">
  <div><strong>Người nhận:</strong> {$safeName} ({$safePhone})</div>
  <div><strong>Địa chỉ giao hàng:</strong> {$safeAddress}</div>
  <div><strong>Phương thức thanh toán:</strong> {$paymentLabel}</div>
  <div><strong>Trạng thái thanh toán:</strong> Chưa thanh toán (Chờ xác nhận)</div>
</div>
<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #e2e8f0; border-radius:6px; overflow:hidden; margin-bottom:14px;">
  <thead>
    <tr style="background:#f1f5f9;">
      <th align="left" style="padding:8px 10px; font-size:12px; color:#475569;">Sản phẩm</th>
      <th align="right" style="padding:8px 10px; font-size:12px; color:#475569;">Thành tiền</th>
    </tr>
  </thead>
  <tbody>
    {$rowsHtml}
    <tr>
      <td style="padding:8px 10px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#475569;">Tạm tính</td>
      <td style="padding:8px 10px; border-bottom:1px solid #e2e8f0; font-size:13px; text-align:right; color:#1e293b;">{$subtotalText}</td>
    </tr>
    {$discountRow}
    <tr style="background:#f8fafc;">
      <td style="padding:10px; font-size:14px; font-weight:800; color:#0f172a;">Tổng thanh toán</td>
      <td style="padding:10px; font-size:15px; font-weight:800; text-align:right; color:#dc2626;">{$finalText}</td>
    </tr>
  </tbody>
</table>
{$bankingHtml}
<p style="margin:18px 0 0; text-align:center;">
  <a href="{$orderUrl}" style="display:inline-block; background:#2454d6; color:#ffffff; text-decoration:none; font-weight:700; font-size:13px; padding:10px 22px; border-radius:6px;">Xem chi tiết đơn hàng</a>
</p>
HTML;

        return self::send(
            $toEmail,
            $subject,
            self::wrapHtmlTemplate($body, 'Email xác nhận đơn hàng tự động từ PCMarket.')
        );
    }

    /**
     * Điều phối gửi email theo driver cấu hình trong .env (smtp | mail | log).
     *
     * @return array{sent: bool, driver: string, error: ?string}
     */
    public static function send(string $toEmail, string $subject, string $htmlBody): array {
        $driver   = strtolower((string)env('MAIL_DRIVER', ''));
        $host     = trim((string)env('MAIL_HOST', ''));
        $username = trim((string)env('MAIL_USERNAME', ''));
        $password = (string)env('MAIL_PASSWORD', '');

        if ($driver === '' || ($driver === 'smtp' && ($host === '' || $username === '' || $password === ''))) {
            $driver = ($host !== '' && $username !== '' && $password !== '') ? 'smtp' : 'log';
        }

        $configuredFrom = trim((string)env('MAIL_FROM_ADDRESS', ''));
        if ($configuredFrom === '' || ($host !== '' && stripos($host, 'gmail.com') !== false && $configuredFrom === 'no-reply@pcmart.vn' && filter_var($username, FILTER_VALIDATE_EMAIL))) {
            $fromAddress = $username !== '' ? $username : 'no-reply@pcmart.vn';
        } else {
            $fromAddress = $configuredFrom;
        }
        $fromName = trim((string)env('MAIL_FROM_NAME', 'PCMarket'));

        if ($driver === 'smtp') {
            try {
                self::sendViaSmtp($toEmail, $subject, $htmlBody, $fromAddress, $fromName);
                return ['sent' => true, 'driver' => 'smtp', 'error' => null];
            } catch (\Throwable $e) {
                self::writeLogFile($toEmail, $subject, $htmlBody, 'SMTP_ERROR: ' . $e->getMessage());
                return ['sent' => false, 'driver' => 'smtp', 'error' => $e->getMessage()];
            }
        }

        if ($driver === 'mail') {
            $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $headers = implode("\r\n", [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: ' . self::formatAddress($fromAddress, $fromName),
            ]);
            $ok = @mail($toEmail, $encodedSubject, $htmlBody, $headers);
            if (!$ok) {
                self::writeLogFile($toEmail, $subject, $htmlBody, 'PHP_MAIL_FAILED');
            }
            return ['sent' => $ok, 'driver' => 'mail', 'error' => $ok ? null : 'PHP mail() failed'];
        }

        self::writeLogFile($toEmail, $subject, $htmlBody, 'LOCAL_MAIL_LOG');
        return ['sent' => true, 'driver' => 'log', 'error' => null];
    }

    private static function wrapHtmlTemplate(string $innerHtml, string $footerNote): string {
        $safeFooter = htmlspecialchars($footerNote, ENT_QUOTES, 'UTF-8');
        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f8fafc; margin:0; padding:24px; color:#1e293b;">
  <div style="max-width:560px; margin:0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:28px;">
    {$innerHtml}
    <hr style="border:none; border-top:1px solid #e2e8f0; margin:18px 0;">
    <p style="margin:0; font-size:12px; color:#94a3b8;">{$safeFooter}</p>
  </div>
</body>
</html>
HTML;
    }

    private static function sendViaSmtp(
        string $toEmail,
        string $subject,
        string $htmlBody,
        string $fromAddress,
        string $fromName
    ): void {
        $host       = trim((string)env('MAIL_HOST', ''));
        $port       = (int)env('MAIL_PORT', 587);
        $username   = trim((string)env('MAIL_USERNAME', ''));
        $password   = (string)env('MAIL_PASSWORD', '');
        $encryption = strtolower(trim((string)env('MAIL_ENCRYPTION', 'tls')));

        if ($host === '') {
            throw new \RuntimeException('Chưa cấu hình MAIL_HOST.');
        }

        $remote = ($encryption === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
        $socket = @stream_socket_client($remote, $errno, $errstr, 12);
        if (!$socket) {
            throw new \RuntimeException("Không thể kết nối SMTP {$remote}: {$errstr} ({$errno})");
        }

        stream_set_timeout($socket, 12);

        try {
            self::expectSmtpCode($socket, [220]);
            self::sendSmtpCommand($socket, 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'), [250]);

            if ($encryption === 'tls') {
                self::sendSmtpCommand($socket, 'STARTTLS', [220]);
                if (!@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new \RuntimeException('Không thể bật mã hóa STARTTLS.');
                }
                self::sendSmtpCommand($socket, 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'), [250]);
            }

            if ($username !== '') {
                self::sendSmtpCommand($socket, 'AUTH LOGIN', [334]);
                self::sendSmtpCommand($socket, base64_encode($username), [334]);
                self::sendSmtpCommand($socket, base64_encode($password), [235]);
            }

            self::sendSmtpCommand($socket, 'MAIL FROM:<' . $fromAddress . '>', [250]);
            self::sendSmtpCommand($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251]);
            self::sendSmtpCommand($socket, 'DATA', [354]);

            $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $message = implode("\r\n", [
                'Date: ' . date('r'),
                'To: <' . $toEmail . '>',
                'From: ' . self::formatAddress($fromAddress, $fromName),
                'Subject: ' . $encodedSubject,
                'MIME-Version: 1.0',
                'Content-Type: text/html; charset=UTF-8',
                'Content-Transfer-Encoding: base64',
                '',
                chunk_split(base64_encode($htmlBody)),
                '.',
            ]);

            fwrite($socket, $message . "\r\n");
            self::expectSmtpCode($socket, [250]);
            @fwrite($socket, "QUIT\r\n");
        } finally {
            fclose($socket);
        }
    }

    private static function sendSmtpCommand($socket, string $command, array $expectedCodes): void {
        fwrite($socket, $command . "\r\n");
        self::expectSmtpCode($socket, $expectedCodes);
    }

    private static function expectSmtpCode($socket, array $expectedCodes): void {
        $response = '';
        while (($line = fgets($socket, 512)) !== false) {
            $response .= $line;
            if (!isset($line[3]) || $line[3] === ' ' || $line[3] === "\r" || $line[3] === "\n") {
                break;
            }
        }
        if ($response === '') {
            throw new \RuntimeException('SMTP không phản hồi hoặc hết thời gian chờ.');
        }
        $code = (int)substr($response, 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            throw new \RuntimeException('SMTP trả về lỗi: ' . trim($response));
        }
    }

    private static function formatAddress(string $email, string $name): string {
        $cleanName = trim(str_replace(["\r", "\n"], '', $name));
        if ($cleanName === '') {
            return "<{$email}>";
        }
        return '=?UTF-8?B?' . base64_encode($cleanName) . "?= <{$email}>";
    }

    /**
     * Lưu log email nội bộ server (bên ngoài public/) phục vụ kiểm thử khi chưa cấu hình SMTP.
     */
    private static function writeLogFile(string $toEmail, string $subject, string $body, string $status): void {
        $logDir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $entry = str_repeat('=', 60) . "\n"
            . 'TIME: ' . date('Y-m-d H:i:s') . " | STATUS: {$status}\n"
            . "TO: {$toEmail}\n"
            . "SUBJECT: {$subject}\n"
            . strip_tags(str_replace(['<br>', '</p>', '</div>', '</h2>'], "\n", $body)) . "\n";
        @file_put_contents($logDir . '/mail.log', $entry, FILE_APPEND | LOCK_EX);
    }
}
