<?php
// src/Controllers/AuthController.php

final class AuthController
{
    public function registerForm(): void
    {
        $title = "Inscription";
        $viewFile = __DIR__ . '/../../views/auth/register.php';
        require __DIR__ . '/../../views/layout.php';
    }

    public function register(): void
    {
        $nom            = trim((string)($_POST['nom'] ?? ''));
        $prenom         = trim((string)($_POST['prenom'] ?? ''));
        $pseudo         = trim((string)($_POST['pseudo'] ?? ''));
        $email          = trim((string)($_POST['email'] ?? ''));
        $password       = (string)($_POST['password'] ?? '');
        $password2      = (string)($_POST['password2'] ?? '');
        $telephone      = trim((string)($_POST['telephone'] ?? ''));
        $adresse        = trim((string)($_POST['adresse'] ?? ''));
        $dateNaissance  = trim((string)($_POST['date_naissance'] ?? ''));
        $photo          = trim((string)($_POST['photo'] ?? ''));

        if ($nom === '' || $prenom === '' || $pseudo === '' || $email === '' || $password === '') {
            $_SESSION['flash_error'] = "Tous les champs obligatoires doivent être renseignés.";
            header("Location: " . BASE_URL . "/register");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = "Email invalide.";
            header("Location: " . BASE_URL . "/register");
            exit;
        }

        if ($password !== $password2) {
            $_SESSION['flash_error'] = "Les mots de passe ne correspondent pas.";
            header("Location: " . BASE_URL . "/register");
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['flash_error'] = "Mot de passe trop court (minimum 8 caractères).";
            header("Location: " . BASE_URL . "/register");
            exit;
        }

        $pdo = Database::pdo();

        $pdo->beginTransaction();

        try {
            $repo = new UtilisateurRepository($pdo);

            if ($repo->findByEmail($email)) {
                throw new Exception("Cet email est déjà utilisé.");
            }
            if ($repo->findByPseudo($pseudo)) {
                throw new Exception("Ce pseudo est déjà utilisé.");
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $newId = $repo->create([
                'nom'            => $nom,
                'prenom'         => $prenom,
                'email'          => $email,
                'password_hash'  => $hash,
                'telephone'      => $telephone,
                'adresse'        => $adresse,
                'date_naissance' => $dateNaissance,
                'photo'          => $photo,
                'pseudo'         => $pseudo,
            ]);

            $repo->addRole((int)$newId, 'UTILISATEUR');
            $repo->addCreditTransaction((int)$newId, null, 20, 'Crédits offerts à la création du compte');

            $pdo->commit();

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/register");
            exit;
        }

        $_SESSION['user'] = [
            'id_utilisateur' => (int)$newId,
            'pseudo'         => $pseudo,
            'email'          => $email,
        ];

        header("Location: " . BASE_URL . "/");
        exit;
    }

    public function loginForm(): void
    {
        $title = "Connexion";
        $viewFile = __DIR__ . '/../../views/auth/login.php';
        require __DIR__ . '/../../views/layout.php';
    }

    public function login(): void
    {
        $email    = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['flash_error'] = "Email et mot de passe requis.";
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $repo = new UtilisateurRepository();
        $user = $repo->findByEmail($email);

        if (!$user) {
            $_SESSION['flash_error'] = "Identifiants invalides.";
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        // Vérification du hash stocké dans utilisateur.password
        if (!password_verify($password, (string)$user['password'])) {
            $_SESSION['flash_error'] = "Identifiants invalides.";
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        // Blocage si compte suspendu
        if ($repo->isSuspended((int)$user['id_utilisateur'])) {
            $_SESSION['flash_error'] = "Votre compte est suspendu.";
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $_SESSION['user'] = [
            'id_utilisateur' => (int)$user['id_utilisateur'],
            'pseudo'         => (string)$user['pseudo'],
            'email'          => (string)$user['email'],
        ];

        header("Location: " . BASE_URL . "/");
        exit;
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();

        header("Location: " . BASE_URL . "/");
        exit;
    }
}
