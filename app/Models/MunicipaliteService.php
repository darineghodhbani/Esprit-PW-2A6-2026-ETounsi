<?php

/** Données métier d'un service municipal ; les requêtes SQL restent dans le contrôleur, comme le reste du portail. */
final class MunicipaliteService {
    public function __construct(private int $id, private string $libelle, private string $typeFormulaire,
        private string $description, private string $pieces, private float $frais, private string $mode) {}
    public static function fromRow(array $r): self { return new self((int)$r['id_service'],(string)$r['libelle'],(string)$r['type_formulaire'],(string)$r['description'],(string)$r['pieces_requises'],(float)$r['frais'],(string)$r['mode_traitement']); }
    public function getId(): int { return $this->id; }
    public function getLibelle(): string { return $this->libelle; }
    public function getTypeFormulaire(): string { return $this->typeFormulaire; }
    public function getDescription(): string { return $this->description; }
    public function getPieces(): string { return $this->pieces; }
    public function getFrais(): float { return $this->frais; }
    public function getMode(): string { return $this->mode; }
}
