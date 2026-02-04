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

        // US11 : savoir si l'utilisateur connecté est le chauffeur de ce covoiturage
        $isDriverOfTrip = false;
        if (isset($_SESSION['user'])) {
            $idDriver = $repo->getDriverIdForTrip($id); // méthode à ajouter dans CovoiturageRepository
            $isDriverOfTrip = ($idDriver !== null) && ((int)$_SESSION['user']['id_utilisateur'] === (int)$idDriver);
        }

        require __DIR__ . '/../../views/covoiturage/detail.php';
    }


    public function participerConfirm(): void
    {
        // 1) Vérifier connexion
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // 2) Lire l'id du covoiturage (dans l'URL: ?id=...)
        $idCovoiturage = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($idCovoiturage <= 0) {
            http_response_code(400);
            echo "Requête invalide (id manquant).";
            return;
        }

        // 3) Charger le covoiturage pour afficher le prix à confirmer
        $repo = new CovoiturageRepository();
        $covoit = $repo->findById($idCovoiturage);

        if (!$covoit) {
            http_response_code(404);
            echo "Covoiturage introuvable.";
            return;
        }

        // 4) Variables attendues par la vue
        $prixCredits = (int)$covoit['prix_personne'];
        $error = null;

        require __DIR__ . '/../../views/covoiturage/confirm_participation.php';
    }

    public function participer(): void
    {
        // 1) Vérifier connexion
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // 2) Récupérer l'id du covoiturage (POST)
        $idCovoiturage = isset($_POST['id_covoiturage']) ? (int)$_POST['id_covoiturage'] : 0;
        if ($idCovoiturage <= 0) {
            http_response_code(400);
            echo "Requête invalide.";
            return;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        // 3) Connexion DB + transaction
        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $covoitRepo = new CovoiturageRepository($pdo);
            $userRepo   = new UtilisateurRepository($pdo);

            // 4) Verrouiller le covoiturage
            $covoit = $covoitRepo->getCovoiturageForUpdate($idCovoiturage);
            if (!$covoit) {
                throw new Exception("Covoiturage introuvable.");
            }

            // 5) Vérifier places restantes
            $nbParticipants = $covoitRepo->countParticipants($idCovoiturage);
            if ($nbParticipants >= (int)$covoit['nb_place']) {
                throw new Exception("Il n'y a plus de places disponibles.");
            }

            // 6) Vérifier si déjà participant
            if ($covoitRepo->isAlreadyParticipant($idUtilisateur, $idCovoiturage)) {
                throw new Exception("Vous participez déjà à ce covoiturage.");
            }

            // 7) Vérifier solde crédits
            $prix = (int)$covoit['prix_personne'];
            $solde = $userRepo->getCreditBalance($idUtilisateur);

            if ($solde < $prix) {
                throw new Exception("Crédits insuffisants.");
            }

            // 8) Débiter les crédits
            $userRepo->addCreditTransaction(
                $idUtilisateur,
                $idCovoiturage,
                -$prix,
                'Participation au covoiturage'
            );

            // 9) Ajouter la participation
            $covoitRepo->addParticipation($idUtilisateur, $idCovoiturage);

            // 10) Tout OK → commit
            $pdo->commit();

            header('Location: ' . BASE_URL . '/covoiturage?id=' . $idCovoiturage);
            exit;

        } catch (Exception $e) {
            // Erreur → rollback
            $pdo->rollBack();

            // Réafficher la page de confirmation avec message
            $error = $e->getMessage();

            $repo = new CovoiturageRepository();
            $covoit = $repo->findById($idCovoiturage);

            $prixCredits = $covoit ? (int)$covoit['prix_personne'] : 0;

            require __DIR__ . '/../../views/covoiturage/confirm_participation.php';
        }
    }

    public function startTrip(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idCovoiturage = isset($_POST['id_covoiturage']) ? (int)$_POST['id_covoiturage'] : 0;
        if ($idCovoiturage <= 0) {
            http_response_code(400);
            echo "Requête invalide.";
            return;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $repo = new CovoiturageRepository($pdo);

            $trip = $repo->getTripById($idCovoiturage);
            if (!$trip) {
                throw new Exception("Covoiturage introuvable.");
            }

            $idDriver = $repo->getDriverIdForTrip($idCovoiturage);
            if ($idDriver === null || (int)$idDriver !== $idUtilisateur) {
                throw new Exception("Action interdite : vous n’êtes pas le chauffeur.");
            }

            if (($trip['statut'] ?? '') !== 'PLANIFIE') {
                throw new Exception("Le covoiturage doit être PLANIFIE pour démarrer.");
            }

            $repo->setTripStatus($idCovoiturage, 'EN_COURS');

            $pdo->commit();

            header('Location: ' . BASE_URL . '/covoiturage?id=' . $idCovoiturage);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(400);
            echo "Erreur : " . htmlspecialchars($e->getMessage());
            return;
        }
    }

    public function finishTrip(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idCovoiturage = isset($_POST['id_covoiturage']) ? (int)$_POST['id_covoiturage'] : 0;
        if ($idCovoiturage <= 0) {
            http_response_code(400);
            echo "Requête invalide.";
            return;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $repo = new CovoiturageRepository($pdo);

            $trip = $repo->getTripById($idCovoiturage);
            if (!$trip) {
                throw new Exception("Covoiturage introuvable.");
            }

            $idDriver = $repo->getDriverIdForTrip($idCovoiturage);
            if ($idDriver === null || (int)$idDriver !== $idUtilisateur) {
                throw new Exception("Action interdite : vous n’êtes pas le chauffeur.");
            }

            if (($trip['statut'] ?? '') !== 'EN_COURS') {
                throw new Exception("Le covoiturage doit être EN_COURS pour terminer.");
            }

            $repo->setTripStatus($idCovoiturage, 'TERMINE');

            // (Optionnel) simulation d'envoi mail aux passagers
            error_log("MAIL(SIMULATION) -> Trajet #{$idCovoiturage} terminé. Demande validation aux passagers.");

            $pdo->commit();

            header('Location: ' . BASE_URL . '/covoiturage?id=' . $idCovoiturage);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(400);
            echo "Erreur : " . htmlspecialchars($e->getMessage());
            return;
        }
    }
}
