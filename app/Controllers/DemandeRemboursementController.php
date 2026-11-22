<?php

require_once __DIR__ . '/CnamController.php';
require_once __DIR__ . '/../Models/DemandeRemboursement.php';
require_once __DIR__ . '/../Models/DecisionCNAM.php';
require_once __DIR__ . '/../Models/DocumentCNAM.php';
require_once __DIR__ . '/../Models/HabilitationCNAM.php';

class DemandeRemboursementController extends CnamController {
    public function liste(): void {
        $user=exigerRole(['citoyen']);
        $this->renderFront('cnam/remboursements',['title'=>tr('Mes remboursements','استرجاع مصاريفي'),'currentPage'=>'mes-remboursements',
            'liste'=>(new DemandeRemboursement())->duCitoyen($user['id'])]);
    }

    public function formulaire(): void {
        $user=exigerRole(['citoyen']); $model=new DemandeRemboursement();
        $id=$this->idQuery(); $old=[];
        if ($id!==null) { $old=$model->trouver($id,$user['id'])??[]; if (!$old || !in_array($old['statut'],['BROUILLON','A_COMPLETER'],true)) { http_response_code(404); exit('Demande introuvable ou non modifiable.'); } }
        $erreurs=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            try {
                $postId=isset($_POST['id_demande'])&&$_POST['id_demande']!==''?CnamValidation::id($_POST['id_demande']):null;
                $deposer=($_POST['action_demande']??'brouillon')==='deposer';
                $model->enregistrer($user['id'],$postId,$_POST,DocumentCNAM::normaliser($_FILES['justificatifs']??[]),$deposer);
                $_SESSION['flash']=['type'=>'success','message'=>$deposer?tr('Votre demande est déposée.','تمّ إيداع طلبك.'):tr('Votre brouillon est enregistré.','تمّ حفظ المسودة.')];
                header('Location: index.php?page=mes-remboursements',true,303); exit;
            } catch (DomainException $e) { $erreurs[]=$e->getMessage(); http_response_code($e->getCode()?:422); }
            catch (Throwable $e) { error_log('Demande CNAM indisponible : '.get_class($e)); $erreurs[]=tr('La demande n’a pas pu être enregistrée.','تعذّر حفظ الطلب.'); http_response_code(503); }
            $old=array_merge($old,$_POST);
        }
        $beneficiaires=array_values(array_filter((new Beneficiaire())->duCitoyen($user['id']),fn($b)=>$b['statut_validation']==='VALIDE'));
        $this->renderFront('cnam/remboursementForm',['title'=>tr('Demander un remboursement','طلب استرجاع مصاريف'),'currentPage'=>'remboursement-form',
            'old'=>$old,'erreurs'=>$erreurs,'beneficiaires'=>$beneficiaires,'documents'=>$id?(new DocumentCNAM())->demande($id):[],
            'creationToken'=>bin2hex(random_bytes(16))]);
    }

    public function supprimer(): void {
        $user=exigerRole(['citoyen']);
        try { (new DemandeRemboursement())->supprimerBrouillon($user['id'],CnamValidation::id($_POST['id_demande']??null),CnamValidation::id($_POST['version']??null));
            $_SESSION['flash']=['type'=>'success','message'=>tr('Le brouillon a été supprimé.','تمّ حذف المسودة.')];
        } catch (DomainException $e) { $_SESSION['flash']=['type'=>'danger','message'=>$e->getMessage()]; }
        header('Location: index.php?page=mes-remboursements',true,303); exit;
    }

    private function agent(): array {
        return $this->utilisateurCNAM();
    }
    public function adminListe(): void {
        $user=$this->agent(); $statut=isset($_GET['statut'])&&is_string($_GET['statut'])?$_GET['statut']:null;
        $liste=(new DemandeRemboursement())->toutes($statut); $ids=array_column($liste,'id_demande');
        $this->renderBack('cnam/demandes',['title'=>'Demandes CNAM','currentPage'=>'admin-demandes-cnam',
            'liste'=>$liste,'documentsParDemande'=>(new DocumentCNAM())->pourDemandes($ids),
            'decisionsParDemande'=>(new DecisionCNAM())->historiquesParDemandes($ids),'peutTraiter'=>$user['role']==='agent']);
    }
    public function prendreEnCharge(): void {
        $user=exigerRole(['agent']);
        try { (new DemandeRemboursement())->prendreEnCharge($user['id'],CnamValidation::id($_POST['id_demande']??null),CnamValidation::id($_POST['version']??null));
            $_SESSION['flash']=['type'=>'success','message'=>'La demande est maintenant à l’étude.']; }
        catch (DomainException $e) { $_SESSION['flash']=['type'=>'danger','message'=>$e->getMessage()]; }
        header('Location: index.php?page=admin-demandes-cnam',true,303); exit;
    }
    public function decision(): void {
        $user=exigerRole(['agent']);
        try { (new DecisionCNAM())->enregistrer($user['id'],CnamValidation::id($_POST['id_demande']??null),CnamValidation::id($_POST['version']??null),$_POST);
            $_SESSION['flash']=['type'=>'success','message'=>'La décision a été enregistrée.']; }
        catch (DomainException $e) { $_SESSION['flash']=['type'=>'danger','message'=>$e->getMessage()]; }
        header('Location: index.php?page=admin-demandes-cnam',true,303); exit;
    }
    public function versement(): void {
        $user=exigerRole(['agent']);
        try { (new DecisionCNAM())->enregistrerVersement($user['id'],CnamValidation::id($_POST['id_demande']??null),CnamValidation::id($_POST['version']??null),$_POST);
            $_SESSION['flash']=['type'=>'success','message'=>'Le versement manuel a été enregistré.']; }
        catch (DomainException $e) { $_SESSION['flash']=['type'=>'danger','message'=>$e->getMessage()]; }
        header('Location: index.php?page=admin-demandes-cnam',true,303); exit;
    }
}
