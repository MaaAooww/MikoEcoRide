# EcoRide – Projet ECF Développeur Web et Web Mobile

Projet réalisé dans le cadre de l’**Évaluation en Cours de Formation (ECF)** du titre professionnel  
**Développeur Web et Web Mobile**.

**EcoRide** est une plateforme de covoiturage écologique, centrée exclusivement sur les déplacements en voiture.  
Elle permet aux utilisateurs de rechercher, consulter, proposer et gérer des trajets, tout en favorisant une mobilité plus responsable.

---

## 1. Contexte du projet

Ce projet a été développé dans le respect du **cahier des charges ECF**, incluant :

- Analyse fonctionnelle et rédaction des User Stories
- Modélisation et création d’une base de données relationnelle
- Développement d’un backend en PHP avec PDO
- Développement d’un frontend structuré et dynamique
- Respect des bonnes pratiques de sécurité et d’architecture

Le projet a été réalisé **sans framework**, afin de démontrer la maîtrise des fondamentaux du développement web.

---

## 2. Fonctionnalités implémentées

### Visiteur

- Accès à la page d’accueil
- Recherche de covoiturages (ville de départ, ville d’arrivée, date)
- Consultation de la liste des covoiturages disponibles
- Consultation du détail d’un covoiturage
- Accès aux pages Contact et Mentions légales
- Création de compte utilisateur

### Utilisateur

- Connexion / déconnexion
- Accès à l’espace personnel
- Gestion et consultation du solde de crédits
- Participation à un covoiturage
- Consultation de l’historique des trajets

### Chauffeur

- Gestion de ses véhicules
- Création de covoiturages
- Consultation de ses trajets
- Validation d’un trajet terminé
- Signalement d’un incident en fin de trajet

### Employé

- Consultation des trajets signalés en incident
- Validation ou refus des incidents
- Application des règles métier associées aux crédits

### Administrateur

- Accès au tableau de bord
- Visualisation des statistiques :
  - nombre de covoiturages par jour
  - crédits gagnés par la plateforme
- Gestion des comptes employés
- Suspension et réactivation de comptes utilisateurs

--> L’ensemble des **US1 à US13** du sujet ECF est implémenté et fonctionnel.

---

## 3. Stack technique

### Frontend

- HTML5
- CSS3 (CSS personnalisé, **sans framework**)
- Layout global unique (Option B – ECF)

### Backend

- PHP 8
- PDO avec requêtes préparées
- Architecture MVC sans framework
- Routeur maison

### Base de données

- MySQL
- Modélisation complète :
  - MCD
  - MPD
  - Script SQL final (`ecoride_schema.sql`)

### Environnement de développement

- XAMPP (Apache + MySQL)
- Visual Studio Code
- Git / GitHub

---

## 4. Architecture du projet

Le projet EcoRide repose sur une architecture MVC stricte, développée sans framework, afin de démontrer la maîtrise des fondamentaux du développement web.

### Arborescence complète du projet

EcoRide
├── public
│ ├── index.php (Front Controller)
│ ├── .htaccess
│ └── assets
│ └── css
│ └── style.css
├── src
│ ├── Core
│ │ ├── Database.php (Connexion PDO)
│ │ ├── Router.php (Routeur maison GET / POST)
│ │ └── Security.php (Sécurité, échappement, helpers)
│ ├── Controllers
│ │ ├── AccountController.php
│ │ ├── AdminController.php
│ │ ├── AuthController.php
│ │ ├── CovoiturageController.php
│ │ ├── EmployeController.php
│ │ ├── HomeController.php
│ │ └── StaticController.php
│ ├── Repositories
│ │ ├── UtilisateurRepository.php
│ │ ├── CovoiturageRepository.php
│ │ └── AdminRepository.php
├── views
│ ├── layout.php (Layout global)
│ ├── home.php
│ ├── account
│ │ ├── index.php
│ │ ├── vehicles.php
│ │ ├── new_trip.php
│ │ ├── history.php
│ │ └── validate_trips.php
│ ├── admin
│ │ ├── dashboard.php
│ │ └── employees.php
│ ├── auth
│ │ ├── login.php
│ │ └── register.php
│ ├── covoiturage
│ │ ├── list.php
│ │ ├── detail.php
│ │ └── confirm_participation.php
│ ├── employe
│ │ └── incidents.php
│ └── static
│ ├── contact.php
│ └── mentions_legales.php
├── config
│ └── config.php
└── README.md

### Principes d’architecture

- **Front Controller** : `public/index.php`
- **Router maison** avec routes exactes GET / POST
- **Layout global unique** : `views/layout.php`
- **Vues organisées par domaine fonctionnel**
- **Accès base de données exclusivement via PDO**
- **Aucun framework externe**

---

## 5. Sécurité et bonnes pratiques

- Mots de passe stockés avec `password_hash()` et vérifiés avec `password_verify()`
- Requêtes SQL préparées (PDO)
- Protection des pages selon les rôles (utilisateur, chauffeur, employé, administrateur)
- Échappement systématique des données affichées (`htmlspecialchars`)
- Respect du cycle POST / REDIRECT / GET

---

## 6. Installation du projet

### Prérequis

- XAMPP (Apache + MySQL)
- PHP 8 ou supérieur
- Navigateur web moderne

### Étapes d’installation

1. Copier ou cloner le projet dans :
   C:\xampp\htdocs\EcoRide

2. Démarrer Apache et MySQL depuis XAMPP
3. Importer la base de données :

- Ouvrir phpMyAdmin
- Créer une base nommée `ecoride`
- Importer le fichier `ecoride_schema.sql`

4. Accéder à l’application :
   http://localhost/EcoRide/public

---
