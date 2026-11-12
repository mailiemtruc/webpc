<?php
namespace App\Modules\Auth;

use App\Core\Model;
use App\Core\Totp;

class UserModel extends Model {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        return $this->whereFirst('email', $email);
    }

    public function registerUser(array $data): int {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['role'] = 'customer';
        $data['status'] = 1;
        $data['email_verified_at'] = null;
        return $this->create($data);
    }

    public function setVerificationToken(int $userId, string $token, int $expireHours = 24): bool {
        $tokenHash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + ($expireHours * 3600));
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `verification_token` = ?, `verification_expires` = ? WHERE `id` = ?");
        return $stmt->execute([$tokenHash, $expires, $userId]);
    }

    public function findByValidVerificationToken(string $token): ?array {
        $tokenHash = hash('sha256', $token);
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `verification_token` = ? AND `verification_expires` > ? LIMIT 1");
        $stmt->execute([$tokenHash, $now]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function markEmailVerified(int $userId): bool {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `email_verified_at` = NOW(), `verification_token` = NULL, `verification_expires` = NULL WHERE `id` = ?");
        return $stmt->execute([$userId]);
    }

    public function setResetToken(string $email, string $token, int $expireMinutes = 30): bool {
        $tokenHash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + ($expireMinutes * 60));
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `reset_token` = ?, `reset_expires` = ? WHERE `email` = ?");
        return $stmt->execute([$tokenHash, $expires, $email]);
    }

    public function findByValidResetToken(string $token): ?array {
        $tokenHash = hash('sha256', $token);
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `reset_token` = ? AND `reset_expires` > ? LIMIT 1");
        $stmt->execute([$tokenHash, $now]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `password` = ?, `reset_token` = NULL, `reset_expires` = NULL WHERE `id` = ?");
        return $stmt->execute([$hash, $userId]);
    }

    public function recordLoginMeta(int $userId, string $ip): void {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `last_login_at` = NOW(), `last_login_ip` = ? WHERE `id` = ?");
        $stmt->execute([substr($ip, 0, 45), $userId]);
    }

    /**
     * Bật xác thực 2 bước (TOTP) và lưu 10 mã dự phòng dùng 1 lần (kiểu Discord).
     *
     * @param array<int, string> $plainBackupCodes
     */
    public function enableTwoFactor(int $userId, string $plainSecret, array $plainBackupCodes): bool {
        $encryptedSecret = Totp::encryptSecret($plainSecret);
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET `two_factor_enabled` = 1, `two_factor_secret` = ?, `two_factor_confirmed_at` = NOW(), `last_totp_slice` = NULL WHERE `id` = ?"
            );
            $stmt->execute([$encryptedSecret, $userId]);

            $this->replaceBackupCodesInternal($userId, $plainBackupCodes);
            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function disableTwoFactor(int $userId): bool {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET `two_factor_enabled` = 0, `two_factor_secret` = NULL, `two_factor_confirmed_at` = NULL, `last_totp_slice` = NULL WHERE `id` = ?"
            );
            $stmt->execute([$userId]);

            $del = $this->db->prepare("DELETE FROM `user_backup_codes` WHERE `user_id` = ?");
            $del->execute([$userId]);

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * @param array<int, string> $plainBackupCodes
     */
    public function replaceBackupCodes(int $userId, array $plainBackupCodes): bool {
        $this->db->beginTransaction();
        try {
            $this->replaceBackupCodesInternal($userId, $plainBackupCodes);
            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function countUnusedBackupCodes(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `user_backup_codes` WHERE `user_id` = ? AND `used_at` IS NULL");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function updateLastTotpSlice(int $userId, int $slice): bool {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `last_totp_slice` = ? WHERE `id` = ?");
        return $stmt->execute([$slice, $userId]);
    }

    /**
     * Kiểm tra và tiêu thụ 1 mã dự phòng chưa sử dụng (trả về số mã còn lại hoặc null nếu không hợp lệ).
     */
    public function consumeBackupCode(int $userId, string $inputCode): ?int {
        $normalized = Totp::normalizeBackupCode($inputCode);
        if (strlen($normalized) !== 8) {
            return null;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("SELECT `id`, `code_hash` FROM `user_backup_codes` WHERE `user_id` = ? AND `used_at` IS NULL FOR UPDATE");
            $stmt->execute([$userId]);
            $rows = $stmt->fetchAll();

            $matchedId = null;
            foreach ($rows as $row) {
                if (password_verify($normalized, $row['code_hash'])) {
                    $matchedId = (int)$row['id'];
                    break;
                }
            }

            if ($matchedId === null) {
                $this->db->rollBack();
                return null;
            }

            $upd = $this->db->prepare("UPDATE `user_backup_codes` SET `used_at` = NOW() WHERE `id` = ? AND `used_at` IS NULL");
            $upd->execute([$matchedId]);
            if ($upd->rowCount() !== 1) {
                $this->db->rollBack();
                return null;
            }

            $this->db->commit();
            return max(0, count($rows) - 1);
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * @param array<int, string> $plainBackupCodes
     */
    private function replaceBackupCodesInternal(int $userId, array $plainBackupCodes): void {
        $del = $this->db->prepare("DELETE FROM `user_backup_codes` WHERE `user_id` = ?");
        $del->execute([$userId]);

        $ins = $this->db->prepare("INSERT INTO `user_backup_codes` (`user_id`, `code_hash`) VALUES (?, ?)");
        foreach ($plainBackupCodes as $code) {
            $normalized = Totp::normalizeBackupCode($code);
            $ins->execute([$userId, password_hash($normalized, PASSWORD_BCRYPT)]);
        }
    }
}
