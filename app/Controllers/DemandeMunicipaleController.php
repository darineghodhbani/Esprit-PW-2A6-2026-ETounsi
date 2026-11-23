<?php
declare(strict_types=1);

/**
 * Compatibilité pour les anciens liens et noms de contrôleur.
 * Les routes municipales actives sont déclarées dans app/routes.php et
 * utilisent MunicipaliteModuleController, qui porte l'implémentation complète.
 */
require_once __DIR__ . '/MunicipaliteModuleController.php';

final class DemandeMunicipaleController extends MunicipaliteModuleController
{
}
