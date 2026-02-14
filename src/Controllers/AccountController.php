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

        $title = "Mon compte";
        $viewFile = __DIR__ . '/../../views/account/index.php';
        require __DIR__ . '/../../views/layout.php';
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

        $title = "Mes véhicules";
        $viewFile = __DIR__ . '/../../views/account/vehicles.php';
        require __DIR__ . '/../../views/layout.php';
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

    public function newTrip(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $repoUser = new UtilisateurRepository();
        $roles = $repoUser->getRoleLibelles($idUtilisateur);

        if (!in_array('CHAUFFEUR', $roles, true)) {
            header('Location: ' . BASE_URL . '/account');
            exit;
        }

        $vehicles = $repoUser->getVehiclesByUser($idUtilisateur);

        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $title = "Créer un covoiturage";
        $viewFile = __DIR__ . '/../../views/account/new_trip.php';
        require __DIR__ . '/../../views/layout.php';
    }

    public function createTrip(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $repoUser = new UtilisateurRepository();
        $roles = $repoUser->getRoleLibelles($idUtilisateur);

        if (!in_array('CHAUFFEUR', $roles, true)) {
            header('Location: ' . BASE_URL . '/account');
            exit;
        }

        // Champs
        $lieuDepart   = trim((string)($_POST['lieu_depart'] ?? ''));
        $lieuArrivee  = trim((string)($_POST['lieu_arrivee'] ?? ''));
        $dateDepart   = trim((string)($_POST['date_depart'] ?? ''));
        $heureDepart  = trim((string)($_POST['heure_depart'] ?? ''));
        $dateArrivee  = trim((string)($_POST['date_arrivee'] ?? ''));
        $heureArrivee = trim((string)($_POST['heure_arrivee'] ?? ''));
        $nbPlace      = (int)($_POST['nb_place'] ?? 0);
        $prixPers     = (int)($_POST['prix_personne'] ?? 0);
        $idVoiture    = (int)($_POST['id_voiture'] ?? 0);

        if ($lieuDepart === '' || $lieuArrivee === '' || $dateDepart === '' || $heureDepart === '' || $nbPlace <= 0 || $idVoiture <= 0) {
            $_SESSION['flash_error'] = "Champs obligatoires manquants (départ/arrivée/date/heure/places/véhicule).";
            header('Location: ' . BASE_URL . '/account/trips/new');
            exit;
        }

        // Vérifie que la voiture appartient au chauffeur (via gere)
        $myVehicles = $repoUser->getVehiclesByUser($idUtilisateur);
        $owned = false;
        foreach ($myVehicles as $v) {
            if ((int)$v['id_voiture'] === $idVoiture) { $owned = true; break; }
        }
        if (!$owned) {
            $_SESSION['flash_error'] = "Véhicule invalide (il doit t’appartenir).";
            header('Location: ' . BASE_URL . '/account/trips/new');
            exit;
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $repoCov = new CovoiturageRepository($pdo);

            $idCovoiturage = $repoCov->create([
                'date_depart'   => $dateDepart,
                'heure_depart'  => $heureDepart,
                'lieu_depart'   => $lieuDepart,
                'date_arrivee'  => $dateArrivee,
                'heure_arrivee' => $heureArrivee,
                'lieu_arrivee'  => $lieuArrivee,
                'statut'        => 'PLANIFIE',
                'nb_place'      => $nbPlace,
                'prix_personne' => $prixPers,
            ]);

            $repoCov->linkVehicle($idCovoiturage, $idVoiture);

            $pdo->commit();

            header('Location: ' . BASE_URL . '/covoiturage?id=' . $idCovoiturage);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_error'] = "Erreur: " . $e->getMessage();
            header('Location: ' . BASE_URL . '/account/trips/new');
            exit;
        }
    }

    public function history(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $allowedStatuses = ['PLANIFIE', 'VALIDE', 'ANNULE', 'TERMINE', 'INCIDENT'];
        $statut = $_GET['statut'] ?? '';
        $statut = in_array($statut, $allowedStatuses, true) ? $statut : '';

        $pdo = Database::pdo();
        $repoCov = new CovoiturageRepository($pdo);
        $repoUser = new UtilisateurRepository($pdo);
 
        $tripsPassenger = $repoCov->findTripsAsPassenger($idUtilisateur, $statut);
        $tripsDriver = $repoCov->findTripsAsDriver($idUtilisateur, $statut);

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $title = "Historique";
        $viewFile = __DIR__ . '/../../views/account/history.php';
        require __DIR__ . '/../../views/layout.php';
    }

    public function cancelParticipation(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];
        $idCovoiturage = (int)($_POST['id_covoiturage'] ?? 0);

        if ($idCovoiturage <= 0) {
            $_SESSION['flash_error'] = "Covoiturage invalide.";
            header('Location: ' . BASE_URL . '/account/history');
            exit;
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $repoCov = new CovoiturageRepository($pdo);
            $repoUser = new UtilisateurRepository($pdo);

            if (!$repoCov->isUserParticipant($idUtilisateur, $idCovoiturage)) {
                throw new Exception("Tu ne participes pas à ce covoiturage.");
            }

            $trip = $repoCov->getTripById($idCovoiturage);
            if (!$trip) {
                throw new Exception("Covoiturage introuvable.");
            }

            // 1) suppression participation
            $repoCov->deleteParticipation($idUtilisateur, $idCovoiturage);

            // 2) remboursement crédits
            $prix = (int)$trip['prix_personne'];
            $repoUser->addCreditTransaction(
                $idUtilisateur,
                $idCovoiturage,
                +$prix,
                "Remboursement annulation participation covoiturage #{$idCovoiturage}"
            );

            // 3) mail (simulation)
            error_log("MAIL(SIMULATION) -> Annulation participation utilisateur #{$idUtilisateur} sur covoiturage #{$idCovoiturage}");

            $pdo->commit();

            $_SESSION['flash_success'] = "Participation annulée. Crédits remboursés (+{$prix}).";
            header('Location: ' . BASE_URL . '/account/history');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_error'] = "Erreur : " . $e->getMessage();
            header('Location: ' . BASE_URL . '/account/history');
            exit;
        }
    }

    public function cancelTripAsDriver(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];
        $idCovoiturage = (int)($_POST['id_covoiturage'] ?? 0);

        if ($idCovoiturage <= 0) {
            $_SESSION['flash_error'] = "Covoiturage invalide.";
            header('Location: ' . BASE_URL . '/account/history');
            exit;
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $repoCov = new CovoiturageRepository($pdo);
            $repoUser = new UtilisateurRepository($pdo);

            if (!$repoCov->isUserDriverOfTrip($idUtilisateur, $idCovoiturage)) {
                throw new Exception("Tu n’es pas le chauffeur de ce covoiturage.");
            }

            $trip = $repoCov->getTripById($idCovoiturage);
            if (!$trip) {
                throw new Exception("Covoiturage introuvable.");
            }

            // 1) Annule le trajet
            $repoCov->setTripStatus($idCovoiturage, 'ANNULE');

            // 2) Rembourse tous les participants
            $prix = (int)$trip['prix_personne'];
            $participants = $repoCov->getParticipantsForTrip($idCovoiturage);

            foreach ($participants as $p) {
                $idP = (int)$p['id_utilisateur'];
                $repoUser->addCreditTransaction(
                    $idP,
                    $idCovoiturage,
                    +$prix,
                    "Remboursement annulation covoiturage #{$idCovoiturage} (chauffeur)"
                );

                // mail (simulation)
                error_log("MAIL(SIMULATION) -> Annulation covoiturage #{$idCovoiturage} vers {$p['email']} ({$p['pseudo']})");
            }

            // 3) Supprime les participations (places libérées)
            $repoCov->deleteAllParticipationsForTrip($idCovoiturage);

            $pdo->commit();

            $_SESSION['flash_success'] = "Covoiturage annulé. Participants remboursés (+{$prix} chacun).";
            header('Location: ' . BASE_URL . '/account/history');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_error'] = "Erreur : " . $e->getMessage();
            header('Location: ' . BASE_URL . '/account/history');
            exit;
        }
    }

    public function validateTrips(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        $pdo = Database::pdo();
        $repoCov = new CovoiturageRepository($pdo);

        $tripsToValidate = $repoCov->findTripsToValidateForUser($idUtilisateur);

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $title = "Valider mes trajets";
        $viewFile = __DIR__ . '/../../views/account/validate_trips.php';
        require __DIR__ . '/../../views/layout.php';
    }

    public function submitTripValidation(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];
        $idCovoiturage = (int)($_POST['id_covoiturage'] ?? 0);
        $action = trim((string)($_POST['action'] ?? '')); // 'VALIDE' ou 'INCIDENT'
        $note = isset($_POST['note']) && $_POST['note'] !== '' ? (int)$_POST['note'] : null;
        $commentaire = trim((string)($_POST['commentaire'] ?? ''));

        if ($idCovoiturage <= 0 || ($action !== 'VALIDE' && $action !== 'INCIDENT')) {
            $_SESSION['flash_error'] = "Données invalides.";
            header('Location: ' . BASE_URL . '/account/validate-trips');
            exit;
        }

        if ($action === 'INCIDENT' && $commentaire === '') {
            $_SESSION['flash_error'] = "Commentaire obligatoire en cas d’incident.";
            header('Location: ' . BASE_URL . '/account/validate-trips');
            exit;
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            $repoCov = new CovoiturageRepository($pdo);
            $repoUser = new UtilisateurRepository($pdo);

            if (!$repoCov->isUserParticipant($idUtilisateur, $idCovoiturage)) {
                throw new Exception("Tu ne participes pas à ce covoiturage.");
            }

            $trip = $repoCov->getTripById($idCovoiturage);
            if (!$trip) {
                throw new Exception("Covoiturage introuvable.");
            }

            if (($trip['statut'] ?? '') !== 'TERMINE') {
                throw new Exception("Ce covoiturage n’est pas en attente de validation.");
            }

            if ($repoCov->hasUserAlreadyVoted($idUtilisateur, $idCovoiturage)) {
                throw new Exception("Tu as déjà envoyé ton avis pour ce covoiturage.");
            }

            if ($action === 'VALIDE') {
                // Note recommandée (1..5). On reste minimal : si vide, on accepte null.
                if ($note !== null && ($note < 1 || $note > 5)) {
                    throw new Exception("La note doit être entre 1 et 5.");
                }
            } else {
                // incident : note optionnelle mais pas obligatoire
            }

            $repoUser->createAvisAndDepose(
                $idUtilisateur,
                $idCovoiturage,
                $commentaire,
                $note,
                $action
            );

            // Recalcule l'état global du covoiturage
            if ($repoCov->hasIncidentForTrip($idCovoiturage)) {
                $repoCov->setTripStatus($idCovoiturage, 'INCIDENT');
            } else {
                $nbParticipants = $repoCov->countParticipants($idCovoiturage);
                $nbAvis = $repoCov->countAvisForTrip($idCovoiturage);

                if ($nbParticipants > 0 && $nbAvis >= $nbParticipants) {
                    // Tous ont validé (et pas d’incident) => VALIDE + crédits chauffeur
                    $repoCov->setTripStatus($idCovoiturage, 'VALIDE');

                    $idDriver = $repoCov->getDriverIdForTrip($idCovoiturage);
                    if ($idDriver === null) {
                        throw new Exception("Chauffeur introuvable pour créditer.");
                    }

                    $gain = (int)$trip['prix_personne'] * $nbParticipants;
                    $repoUser->addCreditTransaction(
                        $idDriver,
                        $idCovoiturage,
                        +$gain,
                        "Gain covoiturage #{$idCovoiturage} (validation passagers)"
                    );
                }
            }

            $pdo->commit();
            $_SESSION['flash_success'] = "Merci, ton retour a été enregistré.";
            header('Location: ' . BASE_URL . '/account/validate-trips');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_error'] = "Erreur : " . $e->getMessage();
            header('Location: ' . BASE_URL . '/account/validate-trips');
            exit;
        }
    }
}
