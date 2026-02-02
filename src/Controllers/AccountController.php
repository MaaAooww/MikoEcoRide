<?php
// src/Controllers/AccountController.php

final class AccountController
{
    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $repo = new UtilisateurRepository();
        $user = $repo->findById($idUtilisateur);
        $roles = $repo->getRoleLibelles($idUtilisateur);
        $solde = $repo->getCreditBalance($idUtilisateur);

        require __DIR__ . '/../../views/account/index.php';
    }

    public function updateDriverRole(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];
        $wantDriver = isset($_POST['is_driver']) && $_POST['is_driver'] === '1';

        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $repo = new UtilisateurRepository($pdo);

            // Toujours garder UTILISATEUR
            $repo->addRole($idUtilisateur, 'UTILISATEUR');

            if ($wantDriver) {
                $repo->addRole($idUtilisateur, 'CHAUFFEUR');
            } else {
                // Retirer CHAUFFEUR si décoché
                $sql = "
                    DELETE p
                    FROM possede p
                    INNER JOIN role r ON r.id_role = p.id_role
                    WHERE p.id_utilisateur = :u AND r.libelle = 'CHAUFFEUR'
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':u' => $idUtilisateur]);
            }

            $pdo->commit();
            header('Location: ' . BASE_URL . '/account');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo "Erreur: " . htmlspecialchars($e->getMessage());
        }
    }

    public function vehicles(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $repo = new UtilisateurRepository();
        $roles = $repo->getRoleLibelles($idUtilisateur);

        // Pour limiter au minimum : on n'autorise la gestion véhicules que si CHAUFFEUR
        if (!in_array('CHAUFFEUR', $roles, true)) {
            header('Location: ' . BASE_URL . '/account');
            exit;
        }

        $vehicles = $repo->getVehiclesByUser($idUtilisateur);
        $marques = $repo->getMarques();

        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        require __DIR__ . '/../../views/account/vehicles.php';
    }

    public function addVehicle(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $repo = new UtilisateurRepository();
        $roles = $repo->getRoleLibelles($idUtilisateur);
        if (!in_array('CHAUFFEUR', $roles, true)) {
            header('Location: ' . BASE_URL . '/account');
            exit;
        }

        $modele = trim((string)($_POST['modele'] ?? ''));
        $immatriculation = trim((string)($_POST['immatriculation'] ?? ''));
        $energie = trim((string)($_POST['energie'] ?? ''));
        $couleur = trim((string)($_POST['couleur'] ?? ''));
        $dateImm = trim((string)($_POST['date_premiere_immatriculation'] ?? ''));
        $idMarque = isset($_POST['id_marque']) ? (int)$_POST['id_marque'] : 0;

        if ($modele === '' || $immatriculation === '' || $energie === '') {
            $_SESSION['flash_error'] = "Modele, immatriculation et énergie sont obligatoires.";
            header('Location: ' . BASE_URL . '/account/vehicles');
            exit;
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $repoTx = new UtilisateurRepository($pdo);

            $repoTx->addVehicleForUser($idUtilisateur, [
                'modele' => $modele,
                'immatriculation' => $immatriculation,
                'energie' => $energie,
                'couleur' => $couleur,
                'date_premiere_immatriculation' => $dateImm,
                'id_marque' => $idMarque > 0 ? $idMarque : null,
            ]);

            $pdo->commit();
            header('Location: ' . BASE_URL . '/account/vehicles');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_error'] = "Erreur: " . $e->getMessage();
            header('Location: ' . BASE_URL . '/account/vehicles');
            exit;
        }
    }
}
