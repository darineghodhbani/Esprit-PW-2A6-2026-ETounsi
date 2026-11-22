<?php
declare(strict_types=1);

require_once __DIR__ . '/CnamController.php';
require_once __DIR__ . '/../Models/AssureSocial.php';

class AssureSocialController extends CnamController {
    public function citoyen(int $id): ?array { return $this->one('SELECT * FROM assure_social WHERE id_citoyen=?',[$id]); }
    public function tous(): array {
        return $this->all('SELECT a.*,c.nom,c.prenom,c.email FROM assure_social a JOIN citoyen c ON c.id_citoyen=a.id_citoyen ORDER BY a.id_assure DESC LIMIT 500');
    }
    public function enregistrer(int $citoyen, array $data): int {
        try {
            return $this->transaction(function () use ($citoyen,$data) {
                if (!$this->one("SELECT id_citoyen FROM citoyen WHERE id_citoyen=? AND actif=1 AND role='citoyen' FOR UPDATE",[$citoyen]))
                    throw new DomainException('Un compte citoyen actif est nécessaire.',403);
                $existing = $this->citoyen($citoyen);
                $d = CnamValidation::assure($data,$existing===null);
                if ($existing) {
                    $this->execute('UPDATE assure_social SET filiere_soins=?,telephone=?,adresse=? WHERE id_assure=?',
                        [$d['filiere_soins'],$d['telephone'],$d['adresse'],$existing['id_assure']]);
                    return (int)$existing['id_assure'];
                }
                $this->execute('INSERT INTO assure_social(id_citoyen,matricule_cnam,filiere_soins,telephone,adresse) VALUES (?,?,?,?,?)',
                    [$citoyen,$d['matricule_cnam'],$d['filiere_soins'],$d['telephone'],$d['adresse']]);
                $id = (int)$this->db->lastInsertId();
                // Le titulaire est le compte vérifié qui a créé le dossier.
                $this->execute("INSERT INTO beneficiaire(id_assure,nom,prenom,date_naissance,lien_parente,statut_validation) VALUES (?,?,?,?,'SOI','VALIDE')",
                    [$id,$d['nom'],$d['prenom'],$d['date_naissance']]);
                return $id;
            });
        } catch (PDOException $e) {
            if (($e->errorInfo[1]??0)===1062) throw new DomainException('Ce matricule ou ce dossier CNAM existe déjà.',409,$e);
            throw $e;
        }
    }

    public function dossier(): void {
        $user = exigerRole(['citoyen']);

        $dossier = $this->citoyen($user['id']);
        $old = $_POST ?: ($dossier ?? []);
        if ($dossier===null) $old += ['nom'=>$user['nom'],'prenom'=>$user['prenom']];
        $erreurs = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->enregistrer($user['id'], $_POST);
                $_SESSION['flash'] = ['type'=>'success','message'=>tr('Votre dossier CNAM est enregistré.','تمّ حفظ ملفّك لدى الصندوق.')];
                header('Location: index.php?page=mon-dossier',true,303); exit;
            } catch (DomainException $e) { $erreurs[] = $e->getMessage(); http_response_code($e->getCode() ?: 422); }
            catch (Throwable $e) { error_log('Dossier CNAM indisponible : ' . get_class($e)); $erreurs[] = tr('Le dossier n’a pas pu être enregistré. Réessayez plus tard.','تعذّر حفظ الملف. حاول مجددًا لاحقًا.'); http_response_code(503); }
            $dossier = $this->citoyen($user['id']);
        }
        $this->renderFront('cnam/monDossier',['title'=>tr('Mon dossier CNAM','ملفي لدى الصندوق'),'currentPage'=>'mon-dossier',
            'dossier'=>$dossier,'old'=>$old,'erreurs'=>$erreurs]);
    }

    public function assures(): void {
        $user = $this->utilisateurCNAM();
        $this->renderBack('cnam/assures',['title'=>'Dossiers assurés · CNAM','currentPage'=>'admin-assures',
            'assures'=>$this->tous()]);
    }
}
