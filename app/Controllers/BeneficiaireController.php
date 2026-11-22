<?php

require_once __DIR__ . '/CnamController.php';
require_once __DIR__ . '/../Models/Beneficiaire.php';
require_once __DIR__ . '/../Models/CnamValidation.php';

class BeneficiaireController extends CnamController {
    public function liste(): void {
        $user=exigerRole(['citoyen']);
        $liste=(new Beneficiaire())->duCitoyen($user['id']);
        $documents=(new DocumentCNAM())->pourBeneficiaires(array_column($liste,'id_beneficiaire'));
        $this->renderFront('cnam/beneficiaires',['title'=>tr('Mes bénéficiaires','المستفيدون'),'currentPage'=>'mes-beneficiaires',
            'liste'=>$liste,'documentsParBeneficiaire'=>$documents]);
    }

    public function formulaire(): void {
        $user=exigerRole(['citoyen']); $model=new Beneficiaire();
        $id=$this->idQuery();
        $owned=$model->duCitoyen($user['id']); $old=[];
        if ($id!==null) foreach ($owned as $beneficiaire) if ((int)$beneficiaire['id_beneficiaire']===$id) $old=$beneficiaire;
        if ($id!==null && !$old) { http_response_code(404); exit('Bénéficiaire introuvable.'); }
        $erreurs=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            try {
                $idPost=isset($_POST['id_beneficiaire']) && $_POST['id_beneficiaire']!==''?CnamValidation::id($_POST['id_beneficiaire']):null;
                $model->enregistrer($user['id'],$idPost,$_POST,DocumentCNAM::normaliser($_FILES['justificatifs']??[]));
                $_SESSION['flash']=['type'=>'success','message'=>tr('Votre demande de rattachement est transmise à la CNAM.','تمّ إرسال طلب الإضافة إلى الصندوق.')];
                header('Location: index.php?page=mes-beneficiaires',true,303); exit;
            } catch (DomainException $e) { $erreurs[]=$e->getMessage(); http_response_code($e->getCode()?:422); }
            catch (Throwable $e) { error_log('Rattachement CNAM indisponible : '.get_class($e)); $erreurs[]=tr('Le rattachement n’a pas pu être enregistré.','تعذّر تسجيل الإضافة.'); http_response_code(503); }
            $old=array_merge($old,$_POST);
        }
        $this->renderFront('cnam/beneficiaireForm',['title'=>tr('Ajouter un bénéficiaire','إضافة مستفيد'),'currentPage'=>'beneficiaire-form',
            'old'=>$old,'erreurs'=>$erreurs,'documents'=>$id? (new DocumentCNAM())->beneficiaire($id):[]]);
    }

    public function supprimer(): void {
        $user=exigerRole(['citoyen']);
        try { (new Beneficiaire())->supprimer($user['id'],CnamValidation::id($_POST['id_beneficiaire']??null));
            $_SESSION['flash']=['type'=>'success','message'=>tr('Le bénéficiaire a été supprimé.','تمّ حذف المستفيد.')];
        } catch (DomainException $e) { $_SESSION['flash']=['type'=>'danger','message'=>$e->getMessage()]; }
        header('Location: index.php?page=mes-beneficiaires',true,303); exit;
    }

    public function adminListe(): void {
        $user=$this->utilisateurCNAM();
        $liste=(new Beneficiaire())->tous();
        $documents=(new DocumentCNAM())->pourBeneficiaires(array_column($liste,'id_beneficiaire'));
        $this->renderBack('cnam/beneficiaires',['title'=>'Bénéficiaires CNAM','currentPage'=>'admin-beneficiaires',
            'liste'=>$liste,'documentsParBeneficiaire'=>$documents,'peutTraiter'=>$user['role']==='agent']);
    }

    public function traiterValidation(): void {
        $user=exigerRole(['agent']);
        try { (new Beneficiaire())->valider($user['id'],CnamValidation::id($_POST['id_beneficiaire']??null),is_string($_POST['statut']??null)?$_POST['statut']:'');
            $_SESSION['flash']=['type'=>'success','message'=>'Le rattachement a été traité.'];
        } catch (DomainException $e) { $_SESSION['flash']=['type'=>'danger','message'=>$e->getMessage()]; }
        header('Location: index.php?page=admin-beneficiaires',true,303); exit;
    }
}
