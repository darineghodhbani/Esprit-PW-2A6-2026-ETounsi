<?php
declare(strict_types=1);
require_once __DIR__ . '/CnamModel.php';
require_once __DIR__ . '/CnamValidation.php';
require_once __DIR__ . '/DocumentCNAM.php';
require_once __DIR__ . '/HabilitationCNAM.php';

class DemandeRemboursement extends CnamModel {
    public const STATUTS = ['BROUILLON','DEPOSEE','EN_ETUDE','A_COMPLETER','ACCEPTEE','REFUSEE','PAYEE'];
    private const JOIN = ' FROM demande_remboursement d JOIN beneficiaire b ON b.id_beneficiaire=d.id_beneficiaire JOIN assure_social a ON a.id_assure=b.id_assure ';
    public function duCitoyen(int $id): array {
        return $this->all('SELECT d.*,b.nom,b.prenom,dc.motif AS motif_decision,dc.montant_accorde' . self::JOIN .
            'LEFT JOIN decision_cnam dc ON dc.id_decision=(SELECT MAX(dx.id_decision) FROM decision_cnam dx WHERE dx.id_demande=d.id_demande) ' .
            'WHERE a.id_citoyen=? ORDER BY d.id_demande DESC LIMIT 500',[$id]);
    }
    public function toutes(?string $statut = null): array {
        $where = in_array($statut,self::STATUTS,true) ? 'WHERE d.statut=?' : '';
        return $this->all('SELECT d.*,b.nom,b.prenom,a.matricule_cnam' . self::JOIN . $where . ' ORDER BY d.id_demande DESC LIMIT 500',$where ? [$statut] : []);
    }
    public function trouver(int $id, ?int $citoyen = null, bool $lock = false): ?array {
        $args = [$id]; $where = 'WHERE d.id_demande=?';
        if ($citoyen !== null) { $where .= ' AND a.id_citoyen=?'; $args[]=$citoyen; }
        return $this->one('SELECT d.*,b.nom,b.prenom,b.date_naissance,b.statut_validation,a.id_assure,a.id_citoyen,a.matricule_cnam' . self::JOIN . $where . ($lock?' FOR UPDATE':''),$args);
    }
    public function enregistrer(int $citoyen, ?int $id, array $data, array $files, bool $deposer): int {
        $stored = []; $documents = new DocumentCNAM($this->db);
        try {
            return $this->transaction(function () use ($citoyen,$id,$data,$files,$deposer,$documents,&$stored) {
                $assure = $this->one('SELECT id_assure FROM assure_social WHERE id_citoyen=? FOR UPDATE',[$citoyen]);
                if (!$assure) throw new DomainException('Créez d’abord votre dossier CNAM.',422);
                $token = $data['creation_token']??'';
                if ($id===null) {
                    if (!is_string($token) || !preg_match('/^[a-f0-9]{32}$/D',$token)) throw new DomainException('Rechargez le formulaire avant de le déposer.',422);
                    $previous = $this->one('SELECT d.id_demande' . self::JOIN . 'WHERE d.creation_token=? AND a.id_citoyen=?',[$token,$citoyen]);
                    if ($previous) return (int)$previous['id_demande'];
                } else {
                    $previous = $this->trouver($id,$citoyen,true);
                    if (!$previous) throw new DomainException('Demande introuvable.',404);
                    if (!in_array($previous['statut'],['BROUILLON','A_COMPLETER'],true)) throw new DomainException('Cette demande ne peut plus être modifiée.',409);
                    if (CnamValidation::id($data['version']??null)!==(int)$previous['version']) throw new DomainException('La demande a changé. Rechargez-la avant de continuer.',409);
                }
                $d = CnamValidation::demande($data);
                $beneficiaire = $this->one('SELECT * FROM beneficiaire WHERE id_beneficiaire=? AND id_assure=? FOR UPDATE',[$d['id_beneficiaire'],$assure['id_assure']]);
                if (!$beneficiaire || $beneficiaire['statut_validation']!=='VALIDE') throw new DomainException('Choisissez un bénéficiaire de votre dossier validé par un agent.',422);
                if ($id!==null && $d['id_beneficiaire']!==(int)$previous['id_beneficiaire']) throw new DomainException('Le bénéficiaire d’une demande existante ne peut pas être remplacé.',409);
                if ($d['date_soins'] < $beneficiaire['date_naissance']) throw new DomainException('Les soins ne peuvent pas précéder la naissance du bénéficiaire.',422);
                $stored = $documents->stocker($files,'JUSTIFICATIF');
                if ($id===null) {
                    $reference = 'CNAM-' . gmdate('Y') . '-' . strtoupper(bin2hex(random_bytes(6)));
                    $this->execute('INSERT INTO demande_remboursement(id_beneficiaire,reference,date_soins,nature_soins,montant_demande,creation_token) VALUES (?,?,?,?,?,?)',
                        [$d['id_beneficiaire'],$reference,$d['date_soins'],$d['nature_soins'],$d['montant_demande'],$token]);
                    $id = (int)$this->db->lastInsertId();
                } else $this->execute('UPDATE demande_remboursement SET date_soins=?,nature_soins=?,montant_demande=?,version=version+1 WHERE id_demande=?',
                    [$d['date_soins'],$d['nature_soins'],$d['montant_demande'],$id]);
                $documents->attacher($d['id_beneficiaire'],$id,$stored);
                if ($deposer) {
                    if (!$documents->demande($id)) throw new DomainException('Ajoutez au moins un justificatif pour déposer la demande.',422);
                    $this->execute("UPDATE demande_remboursement SET statut='DEPOSEE',date_depot=UTC_TIMESTAMP(),version=version+1 WHERE id_demande=?",[$id]);
                }
                return $id;
            });
        } catch (Throwable $e) { DocumentCNAM::effacerFichiers($stored); throw $e; }
    }
    public function prendreEnCharge(int $agent, int $id, int $version): void {
        $this->transaction(function () use ($agent,$id,$version) {
            (new HabilitationCNAM($this->db))->exiger($agent,true);
            $d = $this->trouver($id,null,true);
            if (!$d) throw new DomainException('Demande introuvable.',404);
            if ($d['statut']!=='DEPOSEE' || (int)$d['version']!==$version) throw new DomainException('La demande a changé ou est déjà prise en charge.',409);
            $this->execute("UPDATE demande_remboursement SET statut='EN_ETUDE',version=version+1 WHERE id_demande=?",[$id]);
        });
    }
    public function supprimerBrouillon(int $citoyen, int $id, int $version): void {
        $files = $this->transaction(function () use ($citoyen,$id,$version) {
            $d = $this->trouver($id,$citoyen,true);
            if (!$d) throw new DomainException('Demande introuvable.',404);
            if ($d['statut']!=='BROUILLON' || (int)$d['version']!==$version) throw new DomainException('Seul un brouillon à jour peut être supprimé.',409);
            $files = (new DocumentCNAM($this->db))->demande($id);
            $this->execute('DELETE FROM document_cnam WHERE id_demande=?',[$id]);
            $this->execute('DELETE FROM demande_remboursement WHERE id_demande=?',[$id]); return $files;
        });
        DocumentCNAM::effacerFichiers($files);
    }
}
