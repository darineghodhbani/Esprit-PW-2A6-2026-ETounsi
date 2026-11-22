<?php
declare(strict_types=1);

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/CnamValidation.php';

abstract class CnamController extends Controller {
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

    protected function idQuery(): ?int {
        if (!isset($_GET['id']) || $_GET['id']==='') return null;
        try { return CnamValidation::id($_GET['id']); }
        catch (DomainException $e) { http_response_code(400); exit('Identifiant invalide.'); }
    }
    protected function utilisateurCNAM(): array {
        require_once __DIR__ . '/HabilitationCNAMController.php';
        $user=exigerRole(['agent','admin']);
        if ($user['role']==='agent' && !(new HabilitationCNAMController())->autorise($user['id'])) {
            http_response_code(403);
            $this->renderFront('cnam/nonHabilite',['title'=>'Accès CNAM','currentPage'=>'cnam-refuse']);
            exit;
        }
        return $user;
    }
}
