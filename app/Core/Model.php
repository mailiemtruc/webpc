<?php
namespace App\Core;

use PDO;

abstract class Model {
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function all(string $orderBy = 'id DESC'): array {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    public function find($id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function where(string $column, $value, string $operator = '='): array {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` {$operator} ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public function whereFirst(string $column, $value, string $operator = '='): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` {$operator} ? LIMIT 1");
        $stmt->execute([$value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int {
        $columns = array_keys($data);
        $fields = implode('`, `', $columns);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $sql = "INSERT INTO `{$this->table}` (`{$fields}`) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));

        return (int)$this->db->lastInsertId();
    }

    public function update($id, array $data): bool {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "`{$key}` = ?, ";
        }
        $fields = rtrim($fields, ', ');

        $sql = "UPDATE `{$this->table}` SET {$fields} WHERE `{$this->primaryKey}` = ?";
        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id): bool {
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?");
        return $stmt->execute([$id]);
    }

    public function query(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function queryOne(string $sql, array $params = []): ?array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function count(string $condition = '', array $params = []): int {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}`";
        if (!empty($condition)) {
            $sql .= " WHERE {$condition}";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetch();
        return (int)($res['total'] ?? 0);
    }
}

