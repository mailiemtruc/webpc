<?php
namespace App\Modules\Auth;

use App\Core\Model;

class UserModel extends Model {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        return $this->whereFirst('email', $email);
    }

    public function registerUser(array $data): int {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['role'] = 'customer';
        $data['status'] = 1;
        return $this->create($data);
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
}
