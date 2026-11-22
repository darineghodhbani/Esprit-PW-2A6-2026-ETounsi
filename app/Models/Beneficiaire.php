<?php
declare(strict_types=1);
require_once __DIR__ . '/CnamModel.php';
require_once __DIR__ . '/CnamValidation.php';
require_once __DIR__ . '/DocumentCNAM.php';
require_once __DIR__ . '/HabilitationCNAM.php';

class Beneficiaire extends CnamModel {
    public function duCitoyen(int $citoyen): array {
        return $this->all('SELECT b.* FROM beneficiaire b JOIN assure_social a ON a.id_assure=b.id_assure WHERE a.id_citoyen=? ORDER BY b.id_beneficiaire',[$citoyen]);
    }
    public function tous(): array {
        return $this->all('SELECT b.*,a.matricule_cnam FROM beneficiaire b JOIN assure_social a ON a.id_assure=b.id_assure ORDER BY b.id_beneficiaire DESC LIMIT 500');
    }
    public function trouver(int $id): ?array { return $this->one('SELECT * FROM beneficiaire WHERE id_beneficiaire=?',[$id]); }
    public function enregistrer(int $citoyen, ?int $id, array $data, array $files): int {
        $stored = []; $documents = new DocumentCNAM($this->db);
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
        } catch (Throwable $e) { DocumentCNAM::effacerFichiers($stored); throw $e; }
    }
    public function valider(int $agent, int $id, string $statut): void {
        if (!in_array($statut,['VALIDE','REFUSE'],true)) throw new DomainException('Choisissez une validation proposée.',422);
        $this->transaction(function () use ($agent,$id,$statut) {
            (new HabilitationCNAM($this->db))->exiger($agent,true);
            $b = $this->one('SELECT * FROM beneficiaire WHERE id_beneficiaire=? FOR UPDATE',[$id]);
            if (!$b) throw new DomainException('Bénéficiaire introuvable.',404);
            if ($b['statut_validation']!=='EN_ATTENTE') throw new DomainException('Le rattachement a déjà été traité.',409);
            $this->execute('UPDATE beneficiaire SET statut_validation=? WHERE id_beneficiaire=?',[$statut,$id]);
        });
    }
    public function supprimer(int $citoyen, int $id): void {
        $files = $this->transaction(function () use ($citoyen,$id) {
            $b = $this->one('SELECT b.* FROM beneficiaire b JOIN assure_social a ON a.id_assure=b.id_assure WHERE b.id_beneficiaire=? AND a.id_citoyen=? FOR UPDATE',[$id,$citoyen]);
            if (!$b) throw new DomainException('Bénéficiaire introuvable.',404);
            if ($b['lien_parente']==='SOI' || $b['statut_validation']==='VALIDE' || $this->one('SELECT id_demande FROM demande_remboursement WHERE id_beneficiaire=? LIMIT 1',[$id]))
                throw new DomainException('Conservez ce bénéficiaire : il est validé ou lié à un historique.',409);
            $files = (new DocumentCNAM($this->db))->beneficiaire($id);
            $this->execute('DELETE FROM document_cnam WHERE id_beneficiaire=?',[$id]);
            $this->execute('DELETE FROM beneficiaire WHERE id_beneficiaire=?',[$id]); return $files;
        });
        DocumentCNAM::effacerFichiers($files);
    }
}
