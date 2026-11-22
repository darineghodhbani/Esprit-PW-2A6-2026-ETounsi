<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config.php';

abstract class CnamModel {
    protected PDO $db;
    public function __construct(?PDO $db = null) { $this->db = $db ?? Database::getConnection(); }
    protected function one(string $sql, array $args = []): ?array {
        $q = $this->db->prepare($sql); $q->execute($args);
        return $q->fetch() ?: null;
    }
    protected function all(string $sql, array $args = []): array {
        $q = $this->db->prepare($sql); $q->execute($args); return $q->fetchAll();
    }
    protected function execute(string $sql, array $args = []): void {
        $q = $this->db->prepare($sql); $q->execute($args);
    }
    protected function transaction(callable $action): mixed {
        $this->db->beginTransaction();
        try { $result = $action(); $this->db->commit(); return $result; }
        catch (Throwable $e) { if ($this->db->inTransaction()) $this->db->rollBack(); throw $e; }
    }
}
