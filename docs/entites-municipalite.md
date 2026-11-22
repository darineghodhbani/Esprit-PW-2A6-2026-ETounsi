# Entités du module Municipalité

Les entités `PaiementMunicipal` et `NotificationMunicipale` correspondent aux tables dédiées déjà présentes dans `database/municipalite-integration.sql`. Elles complètent le modèle PHP sans remplacer ni modifier les traitements existants.

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
  MunicipaliteDemande "1" --> "0..1" PaiementMunicipal : donne lieu à
  Citoyen "1" --> "0..*" NotificationMunicipale : reçoit
  MunicipaliteDemande "0..1" --> "0..*" NotificationMunicipale : déclenche
```

Une demande peut avoir au plus un paiement, conformément à la contrainte unique de `municipalite_paiement.id_demande`. Une notification est toujours destinée à un citoyen et peut être liée à une demande. Les reçus d'impression restent générés à partir de la demande et de son paiement : aucune entité d'impression distincte n'est nécessaire.
