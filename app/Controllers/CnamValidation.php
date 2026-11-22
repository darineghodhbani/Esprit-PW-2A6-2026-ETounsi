<?php
declare(strict_types=1);

final class CnamValidation {
    public static function text(mixed $value, string $label, int $max = 150): string {
        if (!is_string($value) || !mb_check_encoding($value,'UTF-8')) throw new DomainException($label . ' est invalide.',422);
        $value = trim($value);
        if ($value === '' || mb_strlen($value) > $max || preg_match('/[<>\x00-\x1f]/u',$value))
            throw new DomainException($label . ' est obligatoire, sans balises, et limité à ' . $max . ' caractères.',422);
        return $value;
    }
    public static function date(mixed $value, string $label): string {
        if (!is_string($value)) throw new DomainException($label . ' est invalide.',422);
        $date = DateTimeImmutable::createFromFormat('!Y-m-d',$value,new DateTimeZone('Africa/Tunis'));
        if (!$date || $date->format('Y-m-d') !== $value || $date > new DateTimeImmutable('today',new DateTimeZone('Africa/Tunis')))
            throw new DomainException($label . ' doit être une date valide, sans être dans le futur.',422);
        return $value;
    }
    public static function money(mixed $value, bool $zero = false): string {
        if (!is_string($value) || !preg_match('/^\d{1,9}(?:[.,]\d{1,3})?$/D',trim($value)))
            throw new DomainException('Saisissez un montant TND avec trois décimales au maximum.',422);
        [$whole,$fraction] = array_pad(explode('.',str_replace(',','.',trim($value))),2,'');
        $amount = ((int)$whole) . '.' . str_pad($fraction,3,'0');
        if (!$zero && self::millimes($amount) === 0) throw new DomainException('Le montant doit être supérieur à zéro.',422);
        return $amount;
    }
    public static function millimes(string $amount): int {
        [$a,$b] = explode('.',$amount); return (int)$a * 1000 + (int)str_pad($b,3,'0');
    }
    public static function id(mixed $value): int {
        $id = (is_int($value) || is_string($value)) ? filter_var($value,FILTER_VALIDATE_INT) : false;
        if ($id === false || $id < 1) throw new DomainException('Identifiant invalide.',400);
        return $id;
    }
    public static function assure(array $d, bool $new): array {
        $out = ['filiere_soins'=>self::text($d['filiere_soins']??null,'La filière',80),
            'telephone'=>self::text($d['telephone']??null,'Le téléphone',25),
            'adresse'=>self::text($d['adresse']??null,'L’adresse',255)];
        if (!preg_match('/^\+?[0-9][0-9 ()-]{6,24}$/D',$out['telephone'])) throw new DomainException('Le téléphone est invalide.',422);
        if ($new) {
            $out['matricule_cnam'] = self::text($d['matricule_cnam']??null,'Le matricule CNAM',32);
            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\/ -]{1,31}$/D',$out['matricule_cnam'])) throw new DomainException('Le matricule CNAM est invalide.',422);
            $out += self::beneficiaire($d + ['lien_parente'=>'SOI'],true);
        }
        return $out;
    }
    public static function beneficiaire(array $d, bool $soi = false): array {
        $lien = $d['lien_parente']??'';
        if (!in_array($lien,$soi ? ['SOI'] : ['CONJOINT','ENFANT','PARENT','AUTRE'],true)) throw new DomainException('Choisissez un lien de parenté valide.',422);
        return ['nom'=>self::text($d['nom']??null,'Le nom',100),'prenom'=>self::text($d['prenom']??null,'Le prénom',100),
            'date_naissance'=>self::date($d['date_naissance']??null,'La date de naissance'),'lien_parente'=>$lien];
    }
    public static function demande(array $d): array {
        return ['id_beneficiaire'=>self::id($d['id_beneficiaire']??null),
            'date_soins'=>self::date($d['date_soins']??null,'La date des soins'),
            'nature_soins'=>self::text($d['nature_soins']??null,'La nature des soins'),
            'montant_demande'=>self::money($d['montant_demande']??null)];
    }
}
