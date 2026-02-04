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

    public function addRole(int $idUtilisateur, string $roleLibelle): void
    {
        // Trouver l'id_role à partir du libellé
        $stmt = $this->pdo->prepare("SELECT id_role FROM role WHERE libelle = :libelle LIMIT 1");
        $stmt->execute([':libelle' => $roleLibelle]);
        $idRole = (int)$stmt->fetchColumn();

        if ($idRole <= 0) {
            throw new Exception("Rôle introuvable: " . $roleLibelle);
        }

        // Eviter doublon
        $sql = "SELECT 1 FROM possede WHERE id_utilisateur = :u AND id_role = :r LIMIT 1";
        $check = $this->pdo->prepare($sql);
        $check->execute([':u' => $idUtilisateur, ':r' => $idRole]);
        if ($check->fetchColumn()) {
            return;
        }

        $ins = $this->pdo->prepare("INSERT INTO possede (id_utilisateur, id_role) VALUES (:u, :r)");
        $ins->execute([':u' => $idUtilisateur, ':r' => $idRole]);
    }

    public function getRoleLibelles(int $idUtilisateur): array
    {
        $sql = "
            SELECT r.libelle
            FROM possede p
            INNER JOIN role r ON r.id_role = p.id_role
            WHERE p.id_utilisateur = :u
            ORDER BY r.libelle
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':u' => $idUtilisateur]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function hasRole(int $idUtilisateur, string $roleLibelle): bool
    {
        $sql = "
            SELECT 1
            FROM possede p
            INNER JOIN role r ON r.id_role = p.id_role
            WHERE p.id_utilisateur = :u AND r.libelle = :lib
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':u' => $idUtilisateur, ':lib' => $roleLibelle]);
        return (bool)$stmt->fetchColumn();
    }

        public function getMarques(): array
    {
        $stmt = $this->pdo->query("SELECT id_marque, libelle FROM marque ORDER BY libelle");
        return $stmt->fetchAll() ?: [];
    }

    public function getVehiclesByUser(int $idUtilisateur): array
    {
        $sql = "
            SELECT
                v.id_voiture, v.modele, v.immatriculation, v.energie, v.couleur, v.date_premiere_immatriculation,
                m.libelle AS marque
            FROM gere g
            INNER JOIN voiture v ON v.id_voiture = g.id_voiture
            LEFT JOIN detient d ON d.id_voiture = v.id_voiture
            LEFT JOIN marque m ON m.id_marque = d.id_marque
            WHERE g.id_utilisateur = :u
            ORDER BY v.id_voiture DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':u' => $idUtilisateur]);
        return $stmt->fetchAll() ?: [];
    }

    public function addVehicleForUser(int $idUtilisateur, array $data): int
    {
        // data: modele, immatriculation, energie, couleur, date_premiere_immatriculation, id_marque|null
        $sql = "
            INSERT INTO voiture (modele, immatriculation, energie, couleur, date_premiere_immatriculation)
            VALUES (:modele, :immatriculation, :energie, :couleur, :date_imm)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':modele' => $data['modele'],
            ':immatriculation' => $data['immatriculation'],
            ':energie' => $data['energie'],
            ':couleur' => $data['couleur'],
            ':date_imm' => $data['date_premiere_immatriculation'],
        ]);

        $idVoiture = (int)$this->pdo->lastInsertId();

        // Lien gere
        $stmt2 = $this->pdo->prepare("INSERT INTO gere (id_utilisateur, id_voiture) VALUES (:u, :v)");
        $stmt2->execute([':u' => $idUtilisateur, ':v' => $idVoiture]);

        // Marque optionnelle
        $idMarque = isset($data['id_marque']) ? (int)$data['id_marque'] : 0;
        if ($idMarque > 0) {
            $stmt3 = $this->pdo->prepare("INSERT INTO detient (id_voiture, id_marque) VALUES (:v, :m)");
            $stmt3->execute([':v' => $idVoiture, ':m' => $idMarque]);
        }

        return $idVoiture;
    }

    public function createAvisAndDepose(int $idUtilisateur, int $idCovoiturage, string $commentaire, ?int $note, string $statut): int
    {
        $sqlAvis = "INSERT INTO avis (id_covoiturage, commentaire, note, statut) VALUES (:covoit, :com, :note, :statut)";
        $stmt = $this->pdo->prepare($sqlAvis);
        $stmt->execute([
            ':covoit' => $idCovoiturage,
            ':com'    => $commentaire,
            ':note'   => $note,
            ':statut' => $statut
        ]);

        $idAvis = (int)$this->pdo->lastInsertId();

        $sqlDepose = "INSERT INTO depose (id_utilisateur, id_avis) VALUES (:u, :a)";
        $stmt2 = $this->pdo->prepare($sqlDepose);
        $stmt2->execute([
            ':u' => $idUtilisateur,
            ':a' => $idAvis
        ]);

        return $idAvis;
    }
}
