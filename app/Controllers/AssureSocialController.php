<?php

require_once __DIR__ . '/CnamController.php';
require_once __DIR__ . '/../Models/AssureSocial.php';
require_once __DIR__ . '/../Models/HabilitationCNAM.php';

class AssureSocialController extends CnamController {
    public function dossier(): void {
        $user = exigerRole(['citoyen']);
        $model = new AssureSocial();
        $dossier = $model->citoyen($user['id']);
        $old = $_POST ?: ($dossier ?? []);
        if ($dossier===null) $old += ['nom'=>$user['nom'],'prenom'=>$user['prenom']];
        $erreurs = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $model->enregistrer($user['id'], $_POST);
                $_SESSION['flash'] = ['type'=>'success','message'=>tr('Votre dossier CNAM est enregistré.','تمّ حفظ ملفّك لدى الصندوق.')];
                header('Location: index.php?page=mon-dossier',true,303); exit;
            } catch (DomainException $e) { $erreurs[] = $e->getMessage(); http_response_code($e->getCode() ?: 422); }
            catch (Throwable $e) { error_log('Dossier CNAM indisponible : ' . get_class($e)); $erreurs[] = tr('Le dossier n’a pas pu être enregistré. Réessayez plus tard.','تعذّر حفظ الملف. حاول مجددًا لاحقًا.'); http_response_code(503); }
            $dossier = $model->citoyen($user['id']);
        }
        $this->renderFront('cnam/monDossier',['title'=>tr('Mon dossier CNAM','ملفي لدى الصندوق'),'currentPage'=>'mon-dossier',
            'dossier'=>$dossier,'old'=>$old,'erreurs'=>$erreurs]);
    }

    public function assures(): void {
        $user = $this->utilisateurCNAM();
        $this->renderBack('cnam/assures',['title'=>'Dossiers assurés · CNAM','currentPage'=>'admin-assures',
            'assures'=>(new AssureSocial())->tous()]);
    }
}
