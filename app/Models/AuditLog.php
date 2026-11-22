<?php

class AuditLog {
    public const ACTIONS = [
        'LOGIN' => 'Connexion réussie',
        'LOGIN_FAILED' => 'Connexion refusée',
        'LOGOUT' => 'Déconnexion',
        'USER_CREATED' => 'Création d’un utilisateur',
        'USER_UPDATED' => 'Modification d’un utilisateur',
        'USER_DELETED' => 'Suppression d’un utilisateur',
        'ROLE_CHANGED' => 'Changement de rôle',
        'ACCOUNT_SUSPENDED' => 'Suspension du compte',
        'ACCOUNT_ACTIVATED' => 'Activation du compte',
        'ACCESS_DENIED' => 'Accès refusé',
    ];
}
