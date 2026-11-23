<?php
declare(strict_types=1);

/**
 * Politique d'autorisation métier du module Municipalité.
 * Les agents reçoivent leurs droits explicitement ; tout accès non accordé est refusé.
 */
final class MunicipaliteAuthorization
{
    public const PERMISSIONS = ['consulter_demandes', 'traiter_demandes'];

    public static function agentHasPermission(int $citoyenId, string $permission): bool
    {
        if ($citoyenId < 1 || !in_array($permission, self::PERMISSIONS, true)) {
            return false;
        }

        $query = Database::getConnection()->prepare(
            'SELECT a.' . $permission . '
             FROM municipalite_agent_access a
             JOIN citoyen c ON c.id_citoyen = a.id_citoyen
             WHERE a.id_citoyen = ? AND c.role = "agent" AND c.actif = 1'
        );
        $query->execute([$citoyenId]);

        return (int) $query->fetchColumn() === 1;
    }

    /** Vérifie un rôle et son droit métier ; l'administrateur garde l'accès complet. */
    public static function can(array $user, string $permission): bool
    {
        if (!in_array($permission, self::PERMISSIONS, true)) {
            return false;
        }
        if (($user['role'] ?? '') === 'admin') {
            return true;
        }
        if (($user['role'] ?? '') !== 'agent') {
            return false;
        }

        return self::agentHasPermission((int) ($user['id_citoyen'] ?? 0), $permission);
    }

    /** Garde serveur à appeler avant toute action métier réservée à l'équipe municipale. */
    public static function requirePermission(int $citoyenId, string $permission): void
    {
        $user = exigerConnexion();
        if (self::can($user, $permission)) {
            return;
        }

        auditEvenement(
            'ACCESS_DENIED',
            'Accès refusé au module Municipalité : permission ' . $permission . '.',
            $citoyenId
        );
        http_response_code(403);
        echo '<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Accès refusé · E-Tounsi</title><main><h1>Accès refusé</h1><p>Votre compte ne possède pas l’autorisation nécessaire pour cette fonction municipale.</p><a href="index.php?page=home">Revenir à l’accueil</a></main></html>';
        exit;
    }
}
