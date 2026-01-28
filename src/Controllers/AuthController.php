<?php
// src/Controllers/AuthController.php

final class AuthController
{
    public function registerForm(): void
    {
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        require __DIR__ . '/../../views/auth/register.php';
    }

    public function register(): void
    {
        // Récup POST
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

        // Validations minimales (côté serveur)
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

        $repo = new UtilisateurRepository();

        // Empêcher doublons (ta table n’a pas de UNIQUE => on le gère côté code)
        if ($repo->findByEmail($email)) {
            $_SESSION['flash_error'] = "Cet email est déjà utilisé.";
            header("Location: " . BASE_URL . "/register");
            exit;
        }
        if ($repo->findByPseudo($pseudo)) {
            $_SESSION['flash_error'] = "Ce pseudo est déjà utilisé.";
            header("Location: " . BASE_URL . "/register");
            exit;
        }

        // Hash bcrypt
        $hash = password_hash($password, PASSWORD_BCRYPT);

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

        // Connexion automatique après inscription
        $_SESSION['user_id'] = $newId;
        $_SESSION['pseudo']  = $pseudo;

        header("Location: " . BASE_URL . "/");
        exit;
    }

    public function loginForm(): void
    {
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        require __DIR__ . '/../../views/auth/login.php';
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

        $_SESSION['user_id'] = (int)$user['id_utilisateur'];
        $_SESSION['pseudo']  = (string)$user['pseudo'];

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
