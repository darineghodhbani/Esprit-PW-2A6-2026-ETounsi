<?php
declare(strict_types=1);

require_once __DIR__ . '/CnamController.php';
require_once __DIR__ . '/../Models/HabilitationCNAM.php';

final class HabilitationCNAMController extends CnamController {
    public function autorise(int $id, bool $lock = false): bool {
        return $this->one("SELECT c.id_citoyen FROM citoyen c JOIN cnam_habilitations h ON h.id_citoyen=c.id_citoyen
            WHERE c.id_citoyen=? AND c.role='agent' AND c.actif=1" . ($lock ? ' FOR UPDATE' : ''), [$id]) !== null;
    }
    public function exiger(int $id, bool $lock = false): void {
        if (!$this->autorise($id, $lock)) throw new DomainException('Cette action est réservée à un agent habilité CNAM.', 403);
    }
    public function agents(): array {
        return $this->all("SELECT c.id_citoyen,c.nom,c.prenom,c.email,h.id_citoyen AS habilite
            FROM citoyen c LEFT JOIN cnam_habilitations h ON h.id_citoyen=c.id_citoyen WHERE c.role='agent' ORDER BY c.nom");
    }
    public function definir(int $admin, int $agent, bool $activer): void {
        $this->transaction(function () use ($admin,$agent,$activer) {
            if (!$this->one("SELECT id_citoyen FROM citoyen WHERE id_citoyen=? AND role='admin' AND actif=1 FOR UPDATE",[$admin]))
                throw new DomainException('Seul un administrateur peut attribuer une habilitation.',403);
            if (!$this->one("SELECT id_citoyen FROM citoyen WHERE id_citoyen=? AND role='agent' AND actif=1 FOR UPDATE",[$agent]))
                throw new DomainException('Choisissez un compte agent actif.',422);
            $this->execute('DELETE FROM cnam_habilitations WHERE id_citoyen=?',[$agent]);
            if ($activer) $this->execute('INSERT INTO cnam_habilitations(id_citoyen) VALUES (?)',[$agent]);
        });
    }

    public function liste(): void {
        exigerRole(['admin']);
        $this->renderBack('cnam/agents',['title'=>'Habilitations CNAM','currentPage'=>'admin-cnam-agents',
            'agents'=>$this->agents()]);
    }
    public function modifier(): void {
        $admin=exigerRole(['admin']);
        try {
            $id=CnamValidation::id($_POST['id_citoyen']??null);
            $this->definir($admin['id'],$id,($_POST['action']??'')==='activer');
            $_SESSION['flash']=['type'=>'success','message'=>'L’habilitation CNAM a été mise à jour.'];
        } catch (DomainException $e) { $_SESSION['flash']=['type'=>'danger','message'=>$e->getMessage()]; }
        header('Location: index.php?page=admin-cnam-agents',true,303); exit;
    }
}
