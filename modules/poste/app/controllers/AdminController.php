<?php

class AdminController extends Controller
{
    public function index()
    {
        $repository = gestionPoste();
        $parcels = $repository->listParcels();
        $statusCounts = [];
        foreach (PosteController::STATUSES as $status) {
            $statusCounts[$status] = 0;
        }
        foreach ($parcels as $parcel) {
            $statusCounts[$parcel['statut']]++;
        }

        $stats = [
            ['label' => 'Colis enregistrés', 'value' => count($parcels), 'icon' => 'box'],
            ['label' => 'En attente', 'value' => $statusCounts['En attente'], 'icon' => 'clock'],
            ['label' => 'En transit', 'value' => $statusCounts['En transit'], 'icon' => 'truck'],
            ['label' => 'Bureaux de poste', 'value' => count($repository->listOffices()), 'icon' => 'geo-alt'],
        ];

        $stats[] = ['label' => 'Utilisateurs', 'value' => count(gestionUtilisateurs()->lister()), 'icon' => 'people'];

        $this->render('dashboard', 'Tableau de bord', 'dashboard', [
            'stats' => $stats,
            'parcels' => array_slice($parcels, 0, 5),
            'statusCounts' => $statusCounts,
        ]);
    }
}
