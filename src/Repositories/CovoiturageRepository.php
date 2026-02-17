<?php
// src/Repositories/CovoiturageRepository.php

final class CovoiturageRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::pdo();
    }

    public function search(
        ?string $depart,
        ?string $arrivee,
        ?string $date,
        ?int $prixMax = null,
        ?bool $ecoOnly = null,
        ?float $noteMin = null,
        ?string $chauffeurPseudo = null,
        ?int $placesMin = null
    ): array
    {
        $sql = "
            SELECT DISTINCT
                c.id_covoiturage,
                c.date_depart,
                c.heure_depart,
                c.lieu_depart,
                c.date_arrivee,
                c.heure_arrivee,
                c.lieu_arrivee,
                c.statut,
                c.nb_place,
                c.prix_personne,

                v.energie,

                u.pseudo AS chauffeur_pseudo,
                n.note_moy AS chauffeur_note,

                GREATEST(c.nb_place - COALESCE(p.nb_participants, 0), 0) AS places_restantes

            FROM covoiturage c
            LEFT JOIN utilise ul ON ul.id_covoiturage = c.id_covoiturage
            LEFT JOIN voiture v  ON v.id_voiture = ul.id_voiture
            LEFT JOIN gere g     ON g.id_voiture = v.id_voiture
            LEFT JOIN utilisateur u ON u.id_utilisateur = g.id_utilisateur

            LEFT JOIN (
                SELECT id_covoiturage, COUNT(*) AS nb_participants
                FROM participe
                GROUP BY id_covoiturage
            ) p ON p.id_covoiturage = c.id_covoiturage

            LEFT JOIN (
                SELECT
                    g2.id_utilisateur AS id_chauffeur,
                    ROUND(AVG(a.note), 1) AS note_moy
                FROM avis a
                INNER JOIN covoiturage c2 ON c2.id_covoiturage = a.id_covoiturage
                INNER JOIN utilise ul2    ON ul2.id_covoiturage = c2.id_covoiturage
                INNER JOIN voiture v2     ON v2.id_voiture = ul2.id_voiture
                INNER JOIN gere g2        ON g2.id_voiture = v2.id_voiture
                WHERE a.statut = 'VALIDE'
                AND a.note IS NOT NULL
                GROUP BY g2.id_utilisateur
            ) n ON n.id_chauffeur = u.id_utilisateur

            WHERE c.statut = 'PLANIFIE'
        ";

        $params = [];

        if ($depart !== null && $depart !== '') {
            $sql .= " AND c.lieu_depart = :depart";
            $params[':depart'] = $depart;
        }

        if ($arrivee !== null && $arrivee !== '') {
            $sql .= " AND c.lieu_arrivee = :arrivee";
            $params[':arrivee'] = $arrivee;
        }

        if ($date !== null && $date !== '') {
            $sql .= " AND c.date_depart = :dateDepart";
            $params[':dateDepart'] = $date;
        }

        if ($prixMax !== null) {
            $sql .= " AND c.prix_personne <= :prixMax";
            $params[':prixMax'] = $prixMax;
        }

        if ($ecoOnly === true) {
            $sql .= " AND (LOWER(v.energie) LIKE '%elect%')";
        }

        if ($noteMin !== null) {
            $sql .= " AND COALESCE(n.note_moy, 0) >= :noteMin";
            $params[':noteMin'] = $noteMin;
        }

        if ($chauffeurPseudo !== null && $chauffeurPseudo !== '') {
            $sql .= " AND u.pseudo = :chauffeurPseudo";
            $params[':chauffeurPseudo'] = $chauffeurPseudo;
        }

        if ($placesMin !== null) {
            $sql .= " AND (c.nb_place - COALESCE(p.nb_participants, 0)) >= :placesMin";
            $params[':placesMin'] = $placesMin;
        }

        // ✅ Conforme US3 : afficher uniquement les covoiturages avec des places restantes
        // Si tu veux conserver aussi ceux complets, commente cette ligne.
        $sql .= " AND (c.nb_place - COALESCE(p.nb_participants, 0)) > 0 ";

        $sql .= " ORDER BY c.date_depart ASC, c.heure_depart ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                c.*,
                v.modele,
                v.immatriculation,
                v.energie,
                v.couleur,
                v.date_premiere_immatriculation,

                ch.id_utilisateur AS chauffeur_id,
                ch.pseudo AS chauffeur_pseudo,
                ch.email AS chauffeur_email,
                ch.photo AS chauffeur_photo,

                (
                    SELECT AVG(a.note)
                    FROM avis a
                    JOIN covoiturage c2 ON c2.id_covoiturage = a.id_covoiturage
                    JOIN utilise u2 ON u2.id_covoiturage = c2.id_covoiturage
                    JOIN gere g2 ON g2.id_voiture = u2.id_voiture
                    WHERE g2.id_utilisateur = ch.id_utilisateur
                      AND a.statut = 'VALIDE'
                      AND a.note IS NOT NULL
                ) AS chauffeur_note_moyenne,

                (
                    SELECT COUNT(*)
                    FROM avis a
                    JOIN covoiturage c2 ON c2.id_covoiturage = a.id_covoiturage
                    JOIN utilise u2 ON u2.id_covoiturage = c2.id_covoiturage
                    JOIN gere g2 ON g2.id_voiture = u2.id_voiture
                    WHERE g2.id_utilisateur = ch.id_utilisateur
                      AND a.statut = 'VALIDE'
                      AND a.note IS NOT NULL
                ) AS chauffeur_nb_avis,

                COALESCE(p.nb_participants, 0) AS places_prises,
                GREATEST(c.nb_place - COALESCE(p.nb_participants, 0), 0) AS places_restantes

            FROM covoiturage c
            LEFT JOIN utilise u ON u.id_covoiturage = c.id_covoiturage
            LEFT JOIN voiture v  ON v.id_voiture = u.id_voiture

            LEFT JOIN gere g ON g.id_voiture = v.id_voiture
            LEFT JOIN utilisateur ch ON ch.id_utilisateur = g.id_utilisateur

            LEFT JOIN (
                SELECT id_covoiturage, COUNT(*) AS nb_participants
                FROM participe
                GROUP BY id_covoiturage
            ) p ON p.id_covoiturage = c.id_covoiturage

            WHERE c.id_covoiturage = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Verrouille le covoiturage pour éviter 2 réservations simultanées (transaction)
    public function getCovoiturageForUpdate(int $idCovoiturage): array|false
    {
        $sql = "
            SELECT *
            FROM covoiturage
            WHERE id_covoiturage = :idCovoiturage
            FOR UPDATE
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idCovoiturage' => $idCovoiturage]);

        return $stmt->fetch();
    }

    // Vérifie si l’utilisateur participe déjà
    public function isAlreadyParticipant(int $idUtilisateur, int $idCovoiturage): bool
    {
        $sql = "
            SELECT 1
            FROM participe
            WHERE id_utilisateur = :idUtilisateur
            AND id_covoiturage = :idCovoiturage
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idUtilisateur' => $idUtilisateur,
            'idCovoiturage' => $idCovoiturage,
        ]);

        return (bool)$stmt->fetchColumn();
    }

    // Insère la participation
    public function addParticipation(int $idUtilisateur, int $idCovoiturage): void
    {
        $sql = "
            INSERT INTO participe (id_utilisateur, id_covoiturage)
            VALUES (:idUtilisateur, :idCovoiturage)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idUtilisateur' => $idUtilisateur,
            'idCovoiturage' => $idCovoiturage,
        ]);
    }

    // Crée un covoiturage et renvoie son id
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO covoiturage
                (date_depart, heure_depart, lieu_depart,
                date_arrivee, heure_arrivee, lieu_arrivee,
                statut, nb_place, prix_personne)
            VALUES
                (:date_depart, :heure_depart, :lieu_depart,
                :date_arrivee, :heure_arrivee, :lieu_arrivee,
                :statut, :nb_place, :prix_personne)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':date_depart'   => $data['date_depart'],
            ':heure_depart'  => $data['heure_depart'],
            ':lieu_depart'   => $data['lieu_depart'],
            ':date_arrivee'  => $data['date_arrivee'],
            ':heure_arrivee' => $data['heure_arrivee'],
            ':lieu_arrivee'  => $data['lieu_arrivee'],
            ':statut'        => $data['statut'],       // ex: PLANIFIE
            ':nb_place'      => $data['nb_place'],
            ':prix_personne' => $data['prix_personne'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    // Lie un véhicule à un covoiturage (table utilise)
    public function linkVehicle(int $idCovoiturage, int $idVoiture): void
    {
        $sql = "INSERT INTO utilise (id_voiture, id_covoiturage) VALUES (:v, :c)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':v' => $idVoiture,
            ':c' => $idCovoiturage,
        ]);
    }

    public function findTripsAsPassenger(int $idUtilisateur, string $statut = ''): array
    {
        $sql = "
            SELECT
                c.*,
                COALESCE(p.nb_participants, 0) AS places_prises,
                GREATEST(c.nb_place - COALESCE(p.nb_participants, 0), 0) AS places_restantes
            FROM covoiturage c
            INNER JOIN participe pa ON pa.id_covoiturage = c.id_covoiturage
            LEFT JOIN (
                SELECT id_covoiturage, COUNT(*) AS nb_participants
                FROM participe
                GROUP BY id_covoiturage
            ) p ON p.id_covoiturage = c.id_covoiturage
            WHERE pa.id_utilisateur = :u
        ";

        $params = [':u' => $idUtilisateur];

        if ($statut !== '') {
            $sql .= " AND c.statut = :statut";
            $params[':statut'] = $statut;
        }

        $sql .= "
            ORDER BY c.date_depart DESC, c.heure_depart DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findTripsAsDriver(int $idUtilisateur, string $statut = ''): array
    {
        $sql = "
            SELECT DISTINCT
                c.*,
                COALESCE(p.nb_participants, 0) AS places_prises,
                GREATEST(c.nb_place - COALESCE(p.nb_participants, 0), 0) AS places_restantes
            FROM covoiturage c
            INNER JOIN utilise u ON u.id_covoiturage = c.id_covoiturage
            INNER JOIN gere g ON g.id_voiture = u.id_voiture
            LEFT JOIN (
                SELECT id_covoiturage, COUNT(*) AS nb_participants
                FROM participe
                GROUP BY id_covoiturage
            ) p ON p.id_covoiturage = c.id_covoiturage
            WHERE g.id_utilisateur = :u
        ";

        $params = [':u' => $idUtilisateur];

        if ($statut !== '') {
            $sql .= " AND c.statut = :statut";
            $params[':statut'] = $statut;
        }

        // ✅ FIN de requête seulement après avoir ajouté le filtre
        $sql .= "
            ORDER BY c.date_depart DESC, c.heure_depart DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isUserDriverOfTrip(int $idUtilisateur, int $idCovoiturage): bool
    {
        $sql = "
            SELECT 1
            FROM covoiturage c
            INNER JOIN utilise u ON u.id_covoiturage = c.id_covoiturage
            INNER JOIN gere g ON g.id_voiture = u.id_voiture
            WHERE c.id_covoiturage = :c AND g.id_utilisateur = :u
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':u' => $idUtilisateur, ':c' => $idCovoiturage]);
        return (bool)$stmt->fetchColumn();
    }

    public function deleteParticipation(int $idUtilisateur, int $idCovoiturage): void
    {
        $sql = "DELETE FROM participe WHERE id_utilisateur = :u AND id_covoiturage = :c";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':u' => $idUtilisateur, ':c' => $idCovoiturage]);
    }

    public function getParticipantsForTrip(int $idCovoiturage): array
    {
        // On récupère email/pseudo si utile pour "mail (simulation)"
        $sql = "
            SELECT u.id_utilisateur, u.email, u.pseudo
            FROM participe p
            INNER JOIN utilisateur u ON u.id_utilisateur = p.id_utilisateur
            WHERE p.id_covoiturage = :c
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':c' => $idCovoiturage]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteAllParticipationsForTrip(int $idCovoiturage): void
    {
        $sql = "DELETE FROM participe WHERE id_covoiturage = :c";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':c' => $idCovoiturage]);
    }

    public function getTripById(int $idCovoiturage): ?array
    {
        $sql = "SELECT * FROM covoiturage WHERE id_covoiturage = :c";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':c' => $idCovoiturage]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function setTripStatus(int $idCovoiturage, string $statut): void
    {
        $sql = "UPDATE covoiturage SET statut = :s WHERE id_covoiturage = :c";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':s' => $statut, ':c' => $idCovoiturage]);
    }

    public function getDriverIdForTrip(int $idCovoiturage): ?int
    {
        $sql = "
            SELECT g.id_utilisateur
            FROM utilise u
            INNER JOIN gere g ON g.id_voiture = u.id_voiture
            WHERE u.id_covoiturage = :c
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':c' => $idCovoiturage]);
        $id = $stmt->fetchColumn();
        return $id !== false ? (int)$id : null;
    }

    // Compte le nombre de participants
    public function countParticipants(int $idCovoiturage): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM participe
            WHERE id_covoiturage = :idCovoiturage
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idCovoiturage' => $idCovoiturage]);

        return (int)$stmt->fetchColumn();
    }

    public function countAvisForTrip(int $idCovoiturage): int
    {
        $sql = "SELECT COUNT(*) FROM avis WHERE id_covoiturage = :c";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':c' => $idCovoiturage]);
        return (int)$stmt->fetchColumn();
    }

    public function hasIncidentForTrip(int $idCovoiturage): bool
    {
        $sql = "SELECT 1 FROM avis WHERE id_covoiturage = :c AND statut = 'INCIDENT' LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':c' => $idCovoiturage]);
        return (bool)$stmt->fetchColumn();
    }

    public function isUserParticipant(int $idUtilisateur, int $idCovoiturage): bool
    {
        $sql = "SELECT 1 FROM participe WHERE id_utilisateur = :u AND id_covoiturage = :c LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':u' => $idUtilisateur, ':c' => $idCovoiturage]);
        return (bool)$stmt->fetchColumn();
    }

    public function hasUserAlreadyVoted(int $idUtilisateur, int $idCovoiturage): bool
    {
        $sql = "
            SELECT 1
            FROM avis a
            INNER JOIN depose d ON d.id_avis = a.id_avis
            WHERE d.id_utilisateur = :u AND a.id_covoiturage = :c
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':u' => $idUtilisateur, ':c' => $idCovoiturage]);
        return (bool)$stmt->fetchColumn();
    }

    public function findTripsToValidateForUser(int $idUtilisateur): array
    {
        $sql = "
            SELECT c.*
            FROM covoiturage c
            INNER JOIN participe p ON p.id_covoiturage = c.id_covoiturage
            WHERE p.id_utilisateur = :u1
            AND c.statut = 'TERMINE'
            AND NOT EXISTS (
                SELECT 1
                FROM avis a
                INNER JOIN depose d ON d.id_avis = a.id_avis
                WHERE d.id_utilisateur = :u2
                    AND a.id_covoiturage = c.id_covoiturage
            )
            ORDER BY c.date_depart DESC, c.heure_depart DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':u1' => $idUtilisateur,
            ':u2' => $idUtilisateur,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findIncidentTrips(): array
    {
        $sql = "
            SELECT
                c.id_covoiturage,
                c.date_depart,
                c.lieu_depart,
                c.date_arrivee,
                c.lieu_arrivee,
                c.statut,
                c.prix_personne,

                uCh.pseudo AS chauffeur_pseudo,
                uCh.email  AS chauffeur_email,

                uPa.pseudo AS passager_pseudo,
                uPa.email  AS passager_email,

                a.note,
                a.commentaire

            FROM covoiturage c
            INNER JOIN avis a ON a.id_covoiturage = c.id_covoiturage

            -- Chauffeur via véhicule du trajet
            INNER JOIN utilise ut ON ut.id_covoiturage = c.id_covoiturage
            INNER JOIN gere g     ON g.id_voiture = ut.id_voiture
            INNER JOIN utilisateur uCh ON uCh.id_utilisateur = g.id_utilisateur

            -- Passager = auteur de l'avis (depose)
            INNER JOIN depose d ON d.id_avis = a.id_avis
            INNER JOIN utilisateur uPa ON uPa.id_utilisateur = d.id_utilisateur

            WHERE c.statut = 'INCIDENT'
            AND a.statut = 'INCIDENT'
            ORDER BY c.id_covoiturage DESC, a.id_avis DESC
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function validateIncidentTrip(int $idCovoiturage): void
    {
        try {
            $this->pdo->beginTransaction();

            // 1) Lock covoiturage
            $covoiturage = $this->getCovoiturageForUpdate($idCovoiturage);
            if (!$covoiturage) {
                throw new Exception("Covoiturage introuvable.");
            }

            // Idempotent : si déjà traité, on sort proprement
            if (($covoiturage['statut'] ?? null) !== 'INCIDENT') {
                $this->pdo->commit();
                return;
            }

            // 2) Chauffeur via méthode existante (utilise -> gere)
            $idChauffeur = $this->getDriverIdForTrip($idCovoiturage);
            if (!$idChauffeur) {
                throw new Exception("Chauffeur introuvable pour ce covoiturage.");
            }

            // 3) Montant à créditer (minimum cohérent)
            $prix = (int)($covoiturage['prix_personne'] ?? 0);
            $nbParticipants = $this->countParticipants($idCovoiturage);

            // La plateforme prélève 2 crédits par participation
            $netParParticipant = max(0, $prix - 2);
            $gain = $netParParticipant * $nbParticipants;

            // 4) Statut -> VALIDE
            $this->setTripStatus($idCovoiturage, 'VALIDE');

            // 5) Créditer chauffeur
            $userRepo = new UtilisateurRepository($this->pdo);
            $userRepo->addCreditTransaction(
                $idChauffeur,
                $idCovoiturage,
                $gain,
                "Validation trajet par employé -2 crédits/participation plateforme"
            );

            $this->pdo->commit();

        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function refuseIncidentTrip(int $idCovoiturage): void
    {
        try {
            $this->pdo->beginTransaction();

            $covoiturage = $this->getCovoiturageForUpdate($idCovoiturage);
            if (!$covoiturage) {
                throw new Exception("Covoiturage introuvable.");
            }

            if (($covoiturage['statut'] ?? null) !== 'INCIDENT') {
                $this->pdo->commit();
                return;
            }

            $this->setTripStatus($idCovoiturage, 'ANNULE');

            $this->pdo->commit();

        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getDepartOptions(): array
    {
        $sql = "
            SELECT DISTINCT lieu_depart AS val
            FROM covoiturage
            WHERE statut = 'PLANIFIE'
            ORDER BY lieu_depart
        ";
        $stmt = $this->pdo->query($sql);
        return array_map(fn($r) => (string)$r['val'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getArriveeOptions(): array
    {
        $sql = "
            SELECT DISTINCT lieu_arrivee AS val
            FROM covoiturage
            WHERE statut = 'PLANIFIE'
            ORDER BY lieu_arrivee
        ";
        $stmt = $this->pdo->query($sql);
        return array_map(fn($r) => (string)$r['val'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getChauffeurOptions(): array
    {
        $sql = "
            SELECT DISTINCT u.pseudo AS val
            FROM covoiturage c
            JOIN utilise ul    ON ul.id_covoiturage = c.id_covoiturage
            JOIN voiture v     ON v.id_voiture = ul.id_voiture
            JOIN gere g        ON g.id_voiture = v.id_voiture
            JOIN utilisateur u ON u.id_utilisateur = g.id_utilisateur
            WHERE c.statut = 'PLANIFIE'
            ORDER BY u.pseudo
        ";
        $stmt = $this->pdo->query($sql);
        return array_map(fn($r) => (string)$r['val'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
