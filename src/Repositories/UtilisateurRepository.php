<?php
// src/Repositories/UtilisateurRepository.php

final class UtilisateurRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::pdo();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByPseudo(string $pseudo): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE pseudo = :pseudo LIMIT 1");
        $stmt->execute([':pseudo' => $pseudo]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        // Champs attendus : nom, prenom, email, password_hash, telephone, adresse, date_naissance, photo, pseudo

        $sql = "
            INSERT INTO utilisateur
              (nom, prenom, email, password, telephone, adresse, date_naissance, photo, pseudo)
            VALUES
              (:nom, :prenom, :email, :password, :telephone, :adresse, :date_naissance, :photo, :pseudo)
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':nom'            => $data['nom'],
            ':prenom'         => $data['prenom'],
            ':email'          => $data['email'],
            ':password'       => $data['password_hash'], // bcrypt
            ':telephone'      => $data['telephone'],
            ':adresse'        => $data['adresse'],
            ':date_naissance' => $data['date_naissance'],
            ':photo'          => $data['photo'],
            ':pseudo'         => $data['pseudo'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    // Retourne le solde de crédits d’un utilisateur (SUM des transactions)
    public function getCreditBalance(int $idUtilisateur): int
    {
        $sql = "
            SELECT COALESCE(SUM(montant), 0) AS solde
            FROM credit_transaction
            WHERE id_utilisateur = :idUtilisateur
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idUtilisateur' => $idUtilisateur]);

        return (int)$stmt->fetchColumn();
    }

    // Ajoute une transaction de crédit (positive ou négative)
    public function addCreditTransaction(
                int $idUtilisateur,
                ?int $idCovoiturage,
                int $montant,
                string $description
            ): void
    {
        $sql = "
            INSERT INTO credit_transaction (id_utilisateur, id_covoiturage, montant, description)
            VALUES (:idUtilisateur, :idCovoiturage, :montant, :description)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idUtilisateur' => $idUtilisateur,
            'idCovoiturage' => $idCovoiturage,
            'montant' => $montant,
            'description' => $description,
        ]);
    }

}
