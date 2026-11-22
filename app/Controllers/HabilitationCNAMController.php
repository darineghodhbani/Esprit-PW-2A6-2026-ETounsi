<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/HabilitationCNAM.php';
require_once __DIR__ . '/../Models/CnamValidation.php';

final class HabilitationCNAMController extends Controller {
    public function liste(): void {
        exigerRole(['admin']);
        $this->renderBack('cnam/agents',['title'=>'Habilitations CNAM','currentPage'=>'admin-cnam-agents',
            'agents'=>(new HabilitationCNAM())->agents()]);
    }
    public function modifier(): void {
        $admin=exigerRole(['admin']);
        try {
            $id=CnamValidation::id($_POST['id_citoyen']??null);
            (new HabilitationCNAM())->definir($admin['id'],$id,($_POST['action']??'')==='activer');
            $_SESSION['flash']=['type'=>'success','message'=>'L’habilitation CNAM a été mise à jour.'];
        } catch (DomainException $e) { $_SESSION['flash']=['type'=>'danger','message'=>$e->getMessage()]; }
        header('Location: index.php?page=admin-cnam-agents',true,303); exit;
    }
}
