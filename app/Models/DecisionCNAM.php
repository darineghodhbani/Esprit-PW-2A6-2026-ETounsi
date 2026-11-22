<?php
declare(strict_types=1);
require_once __DIR__ . '/CnamModel.php';
require_once __DIR__ . '/CnamValidation.php';
require_once __DIR__ . '/DemandeRemboursement.php';

class DecisionCNAM extends CnamModel {
    public function historique(int $demande): array {
        return $this->all('SELECT d.*,c.nom AS agent_nom,c.prenom AS agent_prenom FROM decision_cnam d JOIN citoyen c ON c.id_citoyen=d.id_agent WHERE d.id_demande=? ORDER BY d.id_decision',[$demande]);
    }
    public function historiquesParDemandes(array $ids): array {
        $ids=array_values(array_unique(array_filter(array_map('intval',$ids),static fn($id)=>$id>0)));
        if (!$ids) return [];
        $placeholders=implode(',',array_fill(0,count($ids),'?'));
        $rows=$this->all('SELECT d.*,c.nom AS agent_nom,c.prenom AS agent_prenom FROM decision_cnam d JOIN citoyen c ON c.id_citoyen=d.id_agent WHERE d.id_demande IN ('.$placeholders.') ORDER BY d.id_demande,d.id_decision',$ids);
        $groupes=[]; foreach ($rows as $row) $groupes[(int)$row['id_demande']][]=$row; return $groupes;
    }
    public function enregistrer(int $agent, int $demande, int $version, array $data): void {
        $resultat = $data['resultat']??'';
        if (!in_array($resultat,['A_COMPLETER','ACCEPTEE','REFUSEE'],true)) throw new DomainException('Choisissez un résultat proposé.',422);
        $motif = CnamValidation::text($data['motif']??null,'Le motif',2000);
        $montant = $resultat==='ACCEPTEE' ? CnamValidation::money($data['montant_accorde']??null,true) : null;
        $this->transaction(function () use ($agent,$demande,$version,$resultat,$motif,$montant) {
            (new HabilitationCNAM($this->db))->exiger($agent,true);
            $d = (new DemandeRemboursement($this->db))->trouver($demande,null,true);
            if (!$d) throw new DomainException('Demande introuvable.',404);
            if ($d['statut']!=='EN_ETUDE' || (int)$d['version']!==$version)
                throw new DomainException('La demande a changé. Une décision exige une prise en charge à jour.',409);
            if ($montant!==null && CnamValidation::millimes($montant)>CnamValidation::millimes($d['montant_demande']))
                throw new DomainException('Le montant accordé ne peut pas dépasser le montant demandé.',422);
            if ($resultat==='ACCEPTEE' && $d['statut_validation']!=='VALIDE') throw new DomainException('Le bénéficiaire doit être validé.',409);
            $this->execute('INSERT INTO decision_cnam(id_demande,id_agent,resultat,motif,montant_accorde,date_decision) VALUES (?,?,?,?,?,UTC_TIMESTAMP())',
                [$demande,$agent,$resultat,$motif,$montant]);
            $this->execute('UPDATE demande_remboursement SET statut=?,version=version+1 WHERE id_demande=?',[$resultat,$demande]);
        });
    }
    public function enregistrerVersement(int $agent, int $demande, int $version, array $data): void {
        $date = CnamValidation::date($data['date_versement']??null,'La date du versement');
        $reference = CnamValidation::text($data['reference_versement']??null,'La référence du versement',100);
        $this->transaction(function () use ($agent,$demande,$version,$date,$reference) {
            (new HabilitationCNAM($this->db))->exiger($agent,true);
            $d = (new DemandeRemboursement($this->db))->trouver($demande,null,true);
            if (!$d) throw new DomainException('Demande introuvable.',404);
            if ($d['statut']!=='ACCEPTEE' || (int)$d['version']!==$version) throw new DomainException('Seule une demande acceptée et à jour peut recevoir un versement.',409);
            $last = $this->one('SELECT * FROM decision_cnam WHERE id_demande=? ORDER BY id_decision DESC LIMIT 1',[$demande]);
            if (!$last || $last['resultat']!=='ACCEPTEE' || $last['montant_accorde']===null) throw new DomainException('Une acceptation chiffrée est nécessaire.',409);
            if ($date < substr($last['date_decision'],0,10)) throw new DomainException('Le versement ne peut pas précéder l’acceptation.',422);
            $this->execute("UPDATE demande_remboursement SET statut='PAYEE',date_versement=?,reference_versement=?,version=version+1 WHERE id_demande=?",[$date,$reference,$demande]);
        });
    }
}
