<?php
$couleurs = [
    'En attente' => 'waiting',
    'En transit' => 'transit',
    'Livré' => 'delivered',
    'Annulé' => 'cancelled',
];

$tone = 'unknown';
if (isset($couleurs[$status])) {
    $tone = $couleurs[$status];
}
?>
<span class="poste-status poste-status-<?= e($tone) ?>"><?= e($status) ?></span>
