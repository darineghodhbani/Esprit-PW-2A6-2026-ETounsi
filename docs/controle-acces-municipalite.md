# Métier avancé — contrôle d'accès municipal

La politique serveur est centralisée dans `app/Security/MunicipaliteAuthorization.php`.

| Rôle | Permission métier | Accès |
|---|---|---|
| Administrateur | Toutes les permissions municipales | Gérer les agents, consulter et traiter les demandes |
| Agent sans habilitation | Aucune | Accès refusé par défaut |
| Agent avec `consulter_demandes` | Lecture | Voir les demandes municipales |
| Agent avec `traiter_demandes` | Lecture et traitement | Voir et changer le statut des demandes |
| Citoyen | Aucune permission d'agent | Utiliser uniquement ses propres demandes et reçus |

L'administrateur attribue les permissions depuis `index.php?page=admin-municipalite-agents`. Les contrôles d'autorisation sont aussi appliqués dans le contrôleur et les routes ; masquer un lien dans l'interface ne suffit pas pour obtenir l'accès. Les refus sont consignés dans le journal d'audit.
