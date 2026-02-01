<?php
// src/Repositories/CovoiturageRepository.php

final class CovoiturageRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::pdo();
    }

    public function search(?string $depart, ?string $arrivee, ?string $date, ?int $prixMax = null, ?bool $ecoOnly = null): array
    {
        $sql = "
            SELECT
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
                v.energie
            FROM covoiturage c
            LEFT JOIN utilise u ON u.id_covoiturage = c.id_covoiturage
            LEFT JOIN voiture v  ON v.id_voiture = u.id_voiture
            WHERE 1=1
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
            // Interprétation simple : un voyage est "éco" si energie contient "ELECT"
            $sql .= " AND (v.energie LIKE '%ELECT%')";
        }

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
                v.date_premiere_immatriculation
            FROM covoiturage c
            LEFT JOIN utilise u ON u.id_covoiturage = c.id_covoiturage
            LEFT JOIN voiture v  ON v.id_voiture = u.id_voiture
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
}
