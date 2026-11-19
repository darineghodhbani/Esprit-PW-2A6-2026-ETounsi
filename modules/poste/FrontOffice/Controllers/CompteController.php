<?php

class CompteController
{
    private $gestion;

    public function __construct($gestion)
    {
        $this->gestion = $gestion;
    }

    public function connexion($valeurs)
    {
        return $this->gestion->authentifier($valeurs['email'], $valeurs['password']);
    }

    public function inscription($valeurs)
    {
        return $this->gestion->inscrire(
            $valeurs['nom'],
            $valeurs['email'],
            $valeurs['password'],
            $valeurs['confirmation']
        );
    }

    public function modifierProfil($utilisateur, $valeurs)
    {
        // Le rôle et l'état du compte viennent de la session vérifiée.
        return $this->gestion->modifierProfil(
            $utilisateur['id'],
            $valeurs['nom'],
            $valeurs['email'],
            $valeurs['password'],
            $valeurs['confirmation'],
            $valeurs['mot_de_passe_actuel'],
            $utilisateur['version_session']
        );
    }
}
