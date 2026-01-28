<?php
// src/Repositories/UtilisateurRepository.php

final class UtilisateurRepository
{
    public function findByEmail(string $email): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByPseudo(string $pseudo): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE pseudo = :pseudo LIMIT 1");
        $stmt->execute([':pseudo' => $pseudo]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        // Champs attendus : nom, prenom, email, password_hash, telephone, adresse, date_naissance, photo, pseudo
        $pdo = Database::pdo();

        $sql = "
            INSERT INTO utilisateur
              (nom, prenom, email, password, telephone, adresse, date_naissance, photo, pseudo)
            VALUES
              (:nom, :prenom, :email, :password, :telephone, :adresse, :date_naissance, :photo, :pseudo)
        ";

        $stmt = $pdo->prepare($sql);
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

        return (int)$pdo->lastInsertId();
    }
}
