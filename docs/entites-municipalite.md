# Entités du module Municipalité

Le module municipalité comporte quatre entités métier : `MunicipaliteDemande`, `RendezVous`, `PaiementMunicipal` et `NotificationMunicipale`. Le rendez-vous est représenté séparément dans le modèle PHP, mais ses colonnes restent stockées dans `municipalite_demande` afin de préserver le schéma et les fonctionnalités actuels. Les paiements et notifications correspondent à leurs tables dédiées déjà présentes.

```mermaid
classDiagram
  class Citoyen
  class MunicipaliteService
  class MunicipaliteDemande {
    +int serviceId
    +int citoyenId
    +string telephone
    +string detailsJson
    +string? gouvernorat
    +string? delegation
    +string? dateSouhaitee
  }
  class RendezVous {
    +int idDemande
    +string gouvernorat
    +string delegation
    +string dateSouhaitee
    +string statut
  }
  class PaiementMunicipal {
    +int? idPaiement
    +int idDemande
    +string montant
    +string devise
    +string statut
    +string modePaiement
    +string? reference
    +string? payeLe
  }
  class NotificationMunicipale {
    +int? idNotification
    +int idCitoyen
    +int? idDemande
    +string titre
    +string message
    +bool lue
    +string? creeLe
  }
  Citoyen "1" --> "0..*" MunicipaliteDemande : dépose
  MunicipaliteService "1" --> "0..*" MunicipaliteDemande : concerne
  MunicipaliteDemande "1" --> "0..1" RendezVous : demande selon le service
  MunicipaliteDemande "1" --> "0..1" PaiementMunicipal : donne lieu à
  Citoyen "1" --> "0..*" NotificationMunicipale : reçoit
  MunicipaliteDemande "0..1" --> "0..*" NotificationMunicipale : déclenche
```

Une demande peut avoir au plus un paiement, conformément à la contrainte unique de `municipalite_paiement.id_demande`. Une notification est toujours destinée à un citoyen et peut être liée à une demande. Les reçus d'impression restent générés à partir de la demande et de son paiement : aucune entité d'impression distincte n'est nécessaire.

`RendezVous::fromDemandeRow()` construit l'entité lorsqu'une demande contient une date de rendez-vous ; il renvoie `null` pour les demandes qui ne nécessitent pas de déplacement.
