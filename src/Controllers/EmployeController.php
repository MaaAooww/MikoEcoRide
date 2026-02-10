<?php
// src/Controllers/EmployeController.php

final class EmployeController
{
    public function incidents(): void
    {
        // Auth obligatoire
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        // Rôle EMPLOYE obligatoire
        $userRepo = new UtilisateurRepository();
        if (!$userRepo->hasRole($idUtilisateur, 'EMPLOYE')) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $repo = new CovoiturageRepository();
        $incidents = $repo->findIncidentTrips(); // => tableau de lignes (trajet + chauffeur + passager + avis)

        $title = "Espace Employé – Incidents";
        $viewFile = __DIR__ . '/../../views/employe/incidents.php';
        require __DIR__ . '/../../views/layout.php';
    }

    public function validate(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $userRepo = new UtilisateurRepository();
        if (!$userRepo->hasRole($idUtilisateur, 'EMPLOYE')) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        if (!Security::isPost()) {
            header('Location: ' . BASE_URL . '/employe/incidents');
            exit;
        }

        $idCovoiturage = (int)($_POST['id_covoiturage'] ?? 0);
        if ($idCovoiturage <= 0) {
            header('Location: ' . BASE_URL . '/employe/incidents');
            exit;
        }

        $repo = new CovoiturageRepository();
        $repo->validateIncidentTrip($idCovoiturage);

        header('Location: ' . BASE_URL . '/employe/incidents');
        exit;
    }

    public function refuse(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $userRepo = new UtilisateurRepository();
        if (!$userRepo->hasRole($idUtilisateur, 'EMPLOYE')) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        if (!Security::isPost()) {
            header('Location: ' . BASE_URL . '/employe/incidents');
            exit;
        }

        $idCovoiturage = (int)($_POST['id_covoiturage'] ?? 0);
        if ($idCovoiturage <= 0) {
            header('Location: ' . BASE_URL . '/employe/incidents');
            exit;
        }

        $repo = new CovoiturageRepository();
        $repo->refuseIncidentTrip($idCovoiturage);

        header('Location: ' . BASE_URL . '/employe/incidents');
        exit;
    }
}
