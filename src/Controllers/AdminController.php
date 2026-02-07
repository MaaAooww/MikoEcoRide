<?php
// src/Controllers/AdminController.php

final class AdminController
{
    private function requireAdmin(): int
    {
        // Auth obligatoire
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $idUtilisateur = (int)$_SESSION['user']['id_utilisateur'];

        // Rôle ADMINISTRATEUR obligatoire
        $userRepo = new UtilisateurRepository();
        if (!$userRepo->hasRole($idUtilisateur, 'ADMINISTRATEUR')) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        return $idUtilisateur;
    }

    public function dashboard(): void
    {
        $this->requireAdmin();

        $repo = new AdminRepository();

        $covoituragesParJour = $repo->getCovoituragesParJour(); // [date => nb]
        $creditsPlateformeParJour = $repo->getCreditsPlateformeParJour(); // [date => credits]
        $creditsPlateformeTotal = $repo->getCreditsPlateformeTotal(); // int

        require __DIR__ . '/../../views/admin/dashboard.php';
    }

    public function employees(): void
    {
        $this->requireAdmin();

        $repo = new AdminRepository();
        $employes = $repo->getEmployes(); // liste utilisateurs ayant role EMPLOYE

        require __DIR__ . '/../../views/admin/employees.php';
    }

    public function createEmployee(): void
    {
        $this->requireAdmin();

        if (!Security::isPost()) {
            header('Location: ' . BASE_URL . '/admin/employees');
            exit;
        }

        $pseudo = trim($_POST['pseudo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($pseudo === '' || $email === '' || $password === '') {
            $_SESSION['flash_error'] = "Pseudo, email et mot de passe sont obligatoires.";
            header('Location: ' . BASE_URL . '/admin/employees');
            exit;
        }

        $userRepo = new UtilisateurRepository();

        // Unicité (simple)
        if ($userRepo->findByEmail($email)) {
            $_SESSION['flash_error'] = "Email déjà utilisé.";
            header('Location: ' . BASE_URL . '/admin/employees');
            exit;
        }
        if ($userRepo->findByPseudo($pseudo)) {
            $_SESSION['flash_error'] = "Pseudo déjà utilisé.";
            header('Location: ' . BASE_URL . '/admin/employees');
            exit;
        }

        $newId = $userRepo->create([
            'nom' => null,
            'prenom' => null,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'telephone' => null,
            'adresse' => null,
            'date_naissance' => null,
            'photo' => null,
            'pseudo' => $pseudo,
        ]);

        // Role employé
        $userRepo->addRole((int)$newId, 'EMPLOYE');

        $_SESSION['flash_error'] = "Compte employé créé (id=$newId).";
        header('Location: ' . BASE_URL . '/admin/employees');
        exit;
    }

    public function suspendUser(): void
    {
        $this->requireAdmin();

        if (!Security::isPost()) {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        $id = (int)($_POST['id_utilisateur'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        $userRepo = new UtilisateurRepository();
        $userRepo->setSuspended($id, true);

        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

    public function unsuspendUser(): void
    {
        $this->requireAdmin();

        if (!Security::isPost()) {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        $id = (int)($_POST['id_utilisateur'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        $userRepo = new UtilisateurRepository();
        $userRepo->setSuspended($id, false);

        header('Location: ' . BASE_URL . '/admin');
        exit;
    }
}
