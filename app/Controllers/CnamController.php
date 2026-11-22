<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/HabilitationCNAM.php';
require_once __DIR__ . '/../Models/CnamValidation.php';

abstract class CnamController extends Controller {
    protected function idQuery(): ?int {
        if (!isset($_GET['id']) || $_GET['id']==='') return null;
        try { return CnamValidation::id($_GET['id']); }
        catch (DomainException $e) { http_response_code(400); exit('Identifiant invalide.'); }
    }
    protected function utilisateurCNAM(): array {
        $user=exigerRole(['agent','admin']);
        if ($user['role']==='agent' && !(new HabilitationCNAM())->autorise($user['id'])) {
            http_response_code(403);
            $this->renderFront('cnam/nonHabilite',['title'=>'Accès CNAM','currentPage'=>'cnam-refuse']);
            exit;
        }
        return $user;
    }
}
