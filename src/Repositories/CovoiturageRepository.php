<?php
// src/Repositories/CovoiturageRepository.php

final class CovoiturageRepository
{
    public function search(?string $depart, ?string $arrivee, ?string $date, ?int $prixMax = null, ?bool $ecoOnly = null): array
    {
        $pdo = Database::pdo();

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

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $pdo = Database::pdo();

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

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
