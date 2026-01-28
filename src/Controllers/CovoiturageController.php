<?php
// src/Controllers/CovoiturageController.php

final class CovoiturageController
{
    public function list(): void
    {
        $depart  = isset($_GET['depart']) ? trim((string)$_GET['depart']) : null;
        $arrivee = isset($_GET['arrivee']) ? trim((string)$_GET['arrivee']) : null;
        $date    = isset($_GET['date']) ? trim((string)$_GET['date']) : null;

        // Filtres optionnels (on les ajoutera ensuite dans l’IHM)
        $prixMax = isset($_GET['prixMax']) && $_GET['prixMax'] !== '' ? (int)$_GET['prixMax'] : null;
        $ecoOnly = isset($_GET['eco']) ? (($_GET['eco'] === '1') || ($_GET['eco'] === 'true')) : null;

        $repo = new CovoiturageRepository();
        $results = $repo->search($depart, $arrivee, $date, $prixMax, $ecoOnly);

        require __DIR__ . '/../../views/covoiturage/list.php';
    }

    public function detail(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo "Requête invalide (id manquant).";
            return;
        }

        $repo = new CovoiturageRepository();
        $covoit = $repo->findById($id);

        if (!$covoit) {
            http_response_code(404);
            echo "Covoiturage introuvable.";
            return;
        }

        require __DIR__ . '/../../views/covoiturage/detail.php';
    }
}
