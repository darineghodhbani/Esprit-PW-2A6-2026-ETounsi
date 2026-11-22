<?php
declare(strict_types=1);

require_once __DIR__ . '/CnamController.php';
require_once __DIR__ . '/../Models/Beneficiaire.php';
require_once __DIR__ . '/DocumentCNAMController.php';
require_once __DIR__ . '/HabilitationCNAMController.php';

class BeneficiaireController extends CnamController {
    public function duCitoyen(int $citoyen): array {
        return $this->all('SELECT b.* FROM beneficiaire b JOIN assure_social a ON a.id_assure=b.id_assure WHERE a.id_citoyen=? ORDER BY b.id_beneficiaire',[$citoyen]);
    }
    public function tous(): array {
        return $this->all('SELECT b.*,a.matricule_cnam FROM beneficiaire b JOIN assure_social a ON a.id_assure=b.id_assure ORDER BY b.id_beneficiaire DESC LIMIT 500');
    }
    public function trouver(int $id): ?array { return $this->one('SELECT * FROM beneficiaire WHERE id_beneficiaire=?',[$id]); }
    public function enregistrer(int $citoyen, ?int $id, array $data, array $files): int {
        $stored = []; $documents = new DocumentCNAMController($this->db);
        try {
            return $this->transaction(function () use ($citoyen,$id,$data,$files,$documents,&$stored) {
                $a = $this->one('SELECT * FROM assure_social WHERE id_citoyen=? FOR UPDATE',[$citoyen]);
                if (!$a) throw new DomainException('Créez d’abord votre dossier CNAM.',422);
                if ($id !== null) {
                    $b = $this->one('SELECT * FROM beneficiaire WHERE id_beneficiaire=? AND id_assure=? FOR UPDATE',[$id,$a['id_assure']]);
                    if (!$b) throw new DomainException('Bénéficiaire introuvable.',404);
                    if ($b['lien_parente']==='SOI' || $b['statut_validation']==='VALIDE') throw new DomainException('Ce bénéficiaire ne peut plus être modifié.',409);
                }
                $d = CnamValidation::beneficiaire($data);
                $stored = $documents->stocker($files,'RATTACHEMENT');
                if (!$stored && ($id===null || !$documents->beneficiaire($id))) throw new DomainException('Ajoutez un justificatif de rattachement.',422);
                if ($id===null) {
                    $this->execute('INSERT INTO beneficiaire(id_assure,nom,prenom,date_naissance,lien_parente) VALUES (?,?,?,?,?)',
                        [$a['id_assure'],$d['nom'],$d['prenom'],$d['date_naissance'],$d['lien_parente']]);
                    $id = (int)$this->db->lastInsertId();
                } else $this->execute("UPDATE beneficiaire SET nom=?,prenom=?,date_naissance=?,lien_parente=?,statut_validation='EN_ATTENTE' WHERE id_beneficiaire=?",
                    [$d['nom'],$d['prenom'],$d['date_naissance'],$d['lien_parente'],$id]);
                $documents->attacher($id,null,$stored);
                return $id;
            });
        } catch (Throwable $e) { DocumentCNAMController::effacerFichiers($stored); throw $e; }
    }
    public function valider(int $agent, int $id, string $statut): void {
        if (!in_array($statut,['VALIDE','REFUSE'],true)) throw new DomainException('Choisissez une validation proposée.',422);
        $this->transaction(function () use ($agent,$id,$statut) {
            (new HabilitationCNAMController($this->db))->exiger($agent,true);
            $b = $this->one('SELECT * FROM beneficiaire WHERE id_beneficiaire=? FOR UPDATE',[$id]);
            if (!$b) throw new DomainException('Bénéficiaire introuvable.',404);
            if ($b['statut_validation']!=='EN_ATTENTE') throw new DomainException('Le rattachement a déjà été traité.',409);
            $this->execute('UPDATE beneficiaire SET statut_validation=? WHERE id_beneficiaire=?',[$statut,$id]);
        });
    }
    public function supprimerBeneficiaire(int $citoyen, int $id): void {
        $files = $this->transaction(function () use ($citoyen,$id) {
            $b = $this->one('SELECT b.* FROM beneficiaire b JOIN assure_social a ON a.id_assure=b.id_assure WHERE b.id_beneficiaire=? AND a.id_citoyen=? FOR UPDATE',[$id,$citoyen]);
            if (!$b) throw new DomainException('Bénéficiaire introuvable.',404);
            if ($b['lien_parente']==='SOI' || $b['statut_validation']==='VALIDE' || $this->one('SELECT id_demande FROM demande_remboursement WHERE id_beneficiaire=? LIMIT 1',[$id]))
                throw new DomainException('Conservez ce bénéficiaire : il est validé ou lié à un historique.',409);
            $files = (new DocumentCNAMController($this->db))->beneficiaire($id);
            $this->execute('DELETE FROM document_cnam WHERE id_beneficiaire=?',[$id]);
            $this->execute('DELETE FROM beneficiaire WHERE id_beneficiaire=?',[$id]); return $files;
        });
        DocumentCNAMController::effacerFichiers($files);
    }

    public function liste(): void {
        $user=exigerRole(['citoyen']);
        $liste=$this->duCitoyen($user['id']);
        $documents=(new DocumentCNAMController())->pourBeneficiaires(array_column($liste,'id_beneficiaire'));
        $this->renderFront('cnam/beneficiaires',['title'=>tr('Mes bénéficiaires','المستفيدون'),'currentPage'=>'mes-beneficiaires',
            'liste'=>$liste,'documentsParBeneficiaire'=>$documents]);
    }

    public function formulaire(): void {
        $user=exigerRole(['citoyen']);
        $id=$this->idQuery();
        $owned=$this->duCitoyen($user['id']); $old=[];
        if ($id!==null) foreach ($owned as $beneficiaire) if ((int)$beneficiaire['id_beneficiaire']===$id) $old=$beneficiaire;
        if ($id!==null && !$old) { http_response_code(404); exit('Bénéficiaire introuvable.'); }
        $erreurs=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            try {
                $idPost=isset($_POST['id_beneficiaire']) && $_POST['id_beneficiaire']!==''?CnamValidation::id($_POST['id_beneficiaire']):null;
                $this->enregistrer($user['id'],$idPost,$_POST,DocumentCNAMController::normaliser($_FILES['justificatifs']??[]));
                $_SESSION['flash']=['type'=>'success','message'=>tr('Votre demande de rattachement est transmise à la CNAM.','تمّ إرسال طلب الإضافة إلى الصندوق.')];
                header('Location: index.php?page=mes-beneficiaires',true,303); exit;
            } catch (DomainException $e) { $erreurs[]=$e->getMessage(); http_response_code($e->getCode()?:422); }
            catch (Throwable $e) { error_log('Rattachement CNAM indisponible : '.get_class($e)); $erreurs[]=tr('Le rattachement n’a pas pu être enregistré.','تعذّر تسجيل الإضافة.'); http_response_code(503); }
            $old=array_merge($old,$_POST);
        }
        $this->renderFront('cnam/beneficiaireForm',['title'=>tr('Ajouter un bénéficiaire','إضافة مستفيد'),'currentPage'=>'beneficiaire-form',
            'old'=>$old,'erreurs'=>$erreurs,'documents'=>$id? (new DocumentCNAMController())->beneficiaire($id):[]]);
    }

    public function supprimer(): void {
        $user=exigerRole(['citoyen']);
        try { $this->supprimerBeneficiaire($user['id'],CnamValidation::id($_POST['id_beneficiaire']??null));
            $_SESSION['flash']=['type'=>'success','message'=>tr('Le bénéficiaire a été supprimé.','تمّ حذف المستفيد.')];
        } catch (DomainException $e) { $_SESSION['flash']=['type'=>'danger','message'=>$e->getMessage()]; }
        header('Location: index.php?page=mes-beneficiaires',true,303); exit;
    }

    public function adminListe(): void {
        $user=$this->utilisateurCNAM();
        $liste=$this->tous();
        $documents=(new DocumentCNAMController())->pourBeneficiaires(array_column($liste,'id_beneficiaire'));
        $this->renderBack('cnam/beneficiaires',['title'=>'Bénéficiaires CNAM','currentPage'=>'admin-beneficiaires',
            'liste'=>$liste,'documentsParBeneficiaire'=>$documents,'peutTraiter'=>$user['role']==='agent']);
    }

    public function traiterValidation(): void {
        $user=exigerRole(['agent']);
        try { $this->valider($user['id'],CnamValidation::id($_POST['id_beneficiaire']??null),is_string($_POST['statut']??null)?$_POST['statut']:'');
            $_SESSION['flash']=['type'=>'success','message'=>'Le rattachement a été traité.'];
        } catch (DomainException $e) { $_SESSION['flash']=['type'=>'danger','message'=>$e->getMessage()]; }
        header('Location: index.php?page=admin-beneficiaires',true,303); exit;
    }
}
