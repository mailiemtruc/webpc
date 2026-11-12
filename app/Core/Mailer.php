<?php
namespace App\Core;

/**
 * Trình gửi email hệ thống (hỗ trợ SMTP chuẩn STARTTLS/SSL, PHP mail() và log nội bộ local).
 */
class Mailer {

    /**
     * Gửi email đặt lại mật khẩu.
     *
     * @return array{sent: bool, driver: string, error: ?string}
     */
    public static function sendPasswordReset(string $toEmail, string $toName, string $resetLink): array {
        $appName = env('APP_NAME', 'PCMarket');
        $subject = "[{$appName}] Hướng dẫn đặt lại mật khẩu tài khoản";
        $safeName = htmlspecialchars($toName !== '' ? $toName : 'Quý khách', ENT_QUOTES, 'UTF-8');
        $safeUrl  = htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8');

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f8fafc; margin:0; padding:24px; color:#1e293b;">
  <div style="max-width:560px; margin:0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:28px;">
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
    <hr style="border:none; border-top:1px solid #e2e8f0; margin:18px 0;">
    <p style="margin:0; font-size:12px; color:#94a3b8;">
      Nếu bạn không thực hiện yêu cầu này, hãy bỏ qua email để giữ an toàn cho tài khoản.
    </p>
  </div>
</body>
</html>
HTML;

        return self::send($toEmail, $subject, $htmlBody);
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

        if ($driver === '') {
            $driver = ($host !== '' && $username !== '' && $password !== '') ? 'smtp' : 'log';
        }

        $fromAddress = trim((string)env('MAIL_FROM_ADDRESS', $username !== '' ? $username : 'no-reply@pcmart.vn'));
        $fromName    = trim((string)env('MAIL_FROM_NAME', 'PCMarket'));

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
            if (strlen($line) >= 4 && $line[3] === ' ') {
                break;
            }
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
