<?php
// src/Repositories/AdminRepository.php

final class AdminRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    // Graph 1 : covoiturages par jour (basé sur covoiturage.date_depart)
    public function getCovoituragesParJour(): array
    {
        $sql = "
            SELECT date_depart AS jour, COUNT(*) AS nb
            FROM covoiturage
            GROUP BY date_depart
            ORDER BY date_depart
        ";
        $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $out[(string)$r['jour']] = (int)$r['nb'];
        }
        return $out;
    }

    // Hypothèse ECF : la plateforme gagne 2 crédits par participation sur un trajet VALIDE
    public function getCreditsPlateformeParJour(): array
    {
        $sql = "
            SELECT c.date_depart AS jour, (COUNT(p.id_utilisateur) * 2) AS credits
            FROM covoiturage c
            LEFT JOIN participe p ON p.id_covoiturage = c.id_covoiturage
            WHERE c.statut = 'VALIDE'
            GROUP BY c.date_depart
            ORDER BY c.date_depart
        ";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $out[(string)$r['jour']] = (int)$r['credits'];
        }
        return $out;
    }

    public function getCreditsPlateformeTotal(): int
    {
        $sql = "
            SELECT (COUNT(p.id_utilisateur) * 2) AS credits
            FROM covoiturage c
            JOIN participe p ON p.id_covoiturage = c.id_covoiturage
            WHERE c.statut = 'VALIDE'
        ";
        return (int)$this->pdo->query($sql)->fetchColumn();
    }

    // Liste des employés
    public function getEmployes(): array
    {
        $sql = "
            SELECT u.id_utilisateur, u.pseudo, u.email
            FROM utilisateur u
            JOIN possede po ON po.id_utilisateur = u.id_utilisateur
            JOIN role r ON r.id_role = po.id_role
            WHERE r.libelle = 'EMPLOYE'
            ORDER BY u.id_utilisateur DESC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
