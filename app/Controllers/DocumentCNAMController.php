<?php
declare(strict_types=1);
require_once __DIR__ . '/CnamController.php';
require_once __DIR__ . '/../Models/DocumentCNAM.php';
require_once __DIR__ . '/../Models/HabilitationCNAM.php';
require_once __DIR__ . '/../Models/CnamValidation.php';

final class DocumentCNAMController extends CnamController {
    public function telecharger(): void {
        $user=exigerRole(['citoyen','agent','admin']);
        try { $id=CnamValidation::id($_GET['id']??null); }
        catch (DomainException $e) { http_response_code(400); exit('Identifiant invalide.'); }
        $doc=(new DocumentCNAM())->avecProprietaire($id);
        if (!$doc) { http_response_code(404); exit('Document introuvable.'); }
        if ($user['role']==='citoyen' && (int)$doc['id_citoyen']!==$user['id']) { http_response_code(404); exit('Document introuvable.'); }
        if ($user['role']!=='citoyen') $this->utilisateurCNAM();
        if (!preg_match('/^[a-f0-9]{32}\.(pdf|png|jpg)$/D',$doc['fichier'])) { http_response_code(404); exit('Document introuvable.'); }
        $path=DocumentCNAM::directory().'/'.$doc['fichier'];
        if (!is_file($path) || !is_readable($path)) { http_response_code(404); exit('Document introuvable.'); }
        header('Content-Type: '.$doc['mime']); header('Content-Length: '.filesize($path));
        header('Content-Disposition: attachment; filename="justificatif-'.$id.'.'.pathinfo($path,PATHINFO_EXTENSION).'"');
        header("Content-Security-Policy: default-src 'none'; sandbox"); header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store'); readfile($path); exit;
    }
}
