# Entités du module Municipalité

Le diagramme présente exactement les quatre entités demandées : `DemandeMunicipale`, `RendezVous`, `PaiementMunicipal` et `NotificationMunicipale`. Le service et le citoyen sont représentés par leurs identifiants étrangers dans ces entités et ne sont donc pas ajoutés comme classes au diagramme. `RendezVous` possède sa propre table SQL `municipalite_rendez_vous`. Les rendez-vous historiques sont migrés depuis `municipalite_demande` sans suppression des anciennes colonnes.

```mermaid
classDiagram
  class DemandeMunicipale {
    +int serviceId
    +int citoyenId
    +string telephone
    +string detailsJson
    +string? gouvernorat
    +string? delegation
    +string? dateSouhaitee
  }
  class RendezVous {
    +int? idRendezVous
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
  DemandeMunicipale "1" --> "0..1" RendezVous : demande selon le service
  DemandeMunicipale "1" --> "0..1" PaiementMunicipal : donne lieu à
  DemandeMunicipale "0..1" --> "0..*" NotificationMunicipale : déclenche
```

Une demande peut avoir au plus un rendez-vous et au plus un paiement, conformément aux contraintes uniques de `municipalite_rendez_vous.id_demande` et `municipalite_paiement.id_demande`. Une notification est toujours destinée à un citoyen et peut être liée à une demande. Les reçus d'impression restent générés à partir de la demande et de son paiement : aucune entité d'impression distincte n'est nécessaire.

`RendezVous::fromRow()` construit l'entité depuis sa table dédiée.
