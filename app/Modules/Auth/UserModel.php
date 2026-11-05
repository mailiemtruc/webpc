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
}

