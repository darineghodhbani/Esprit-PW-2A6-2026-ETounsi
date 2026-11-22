<?php
declare(strict_types=1);

/** Entité métier du rendez-vous demandé pour une démarche municipale. */
final class RendezVous
{
    public const STATUTS = ['en_attente', 'confirme', 'refuse', 'annule'];

    public function __construct(
        private int $idDemande,
        private string $gouvernorat,
        private string $delegation,
        private string $dateSouhaitee,
        private string $statut = 'en_attente',
    ) {
        if (!in_array($this->statut, self::STATUTS, true)) {
            throw new InvalidArgumentException('Statut de rendez-vous municipal invalide.');
        }
    }

    /** Construit le rendez-vous à partir de la ligne de demande déjà enregistrée. */
    public static function fromDemandeRow(array $row): ?self
    {
        if (empty($row['date_souhaitee'])) {
            return null;
        }

        return new self(
            (int) $row['id_demande'],
            (string) ($row['gouvernorat'] ?? ''),
            (string) ($row['delegation'] ?? ''),
            (string) $row['date_souhaitee'],
            (string) ($row['statut_rdv'] ?? 'en_attente'),
        );
    }

    /** L'identifiant du rendez-vous est celui de sa demande, selon le schéma actuel. */
    public function getIdDemande(): int { return $this->idDemande; }
    public function getGouvernorat(): string { return $this->gouvernorat; }
    public function getDelegation(): string { return $this->delegation; }
    public function getDateSouhaitee(): string { return $this->dateSouhaitee; }
    public function getStatut(): string { return $this->statut; }
}
