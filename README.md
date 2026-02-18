# EcoRide – Projet ECF Développeur Web et Web Mobile

Projet réalisé dans le cadre de l’Évaluation en Cours de Formation (ECF)  
Titre professionnel : Développeur Web et Web Mobile (RNCP 37674 – Niveau 5)

EcoRide est une application web de covoiturage écologique dédiée exclusivement aux déplacements en voiture.

---

# 1. Contexte et objectifs

L’objectif du projet est de concevoir et développer une application web complète répondant aux exigences du cahier des charges officiel EcoRide.

Le projet couvre :

- L’analyse des besoins (User Stories US1 à US13)
- La conception de la base de données relationnelle
- Le développement Front-End et Back-End
- La gestion des rôles et des règles métier
- La sécurisation de l’application
- La documentation complète
- La gestion de projet en Kanban
- La préparation au déploiement

---

# 2. Liens officiels

## Dépôt GitHub public

https://github.com/NataliaRoblin/Ecoride/tree/main

## Gestion de projet (Kanban Trello)

https://trello.com/invite/b/6994d83f077112a9c2ebdb87/ATTIde0238392ceeb204fdc015816ed3eeb32F8B38E5/ecf-ecoride

---

# 3. Démarche technique

## Architecture

L’application repose sur une architecture MVC simplifiée développée sans framework afin de démontrer la maîtrise des fondamentaux :

- Séparation des responsabilités (Controllers / Views / Repositories)
- Routeur maison pour gestion des requêtes GET / POST
- Accès aux données via PDO
- Centralisation de la connexion base de données

## Choix techniques justifiés

- PHP natif : permet de démontrer la compréhension des mécanismes internes (sessions, routing, sécurité).
- PDO : sécurisation via requêtes préparées.
- Bootstrap : rapidité de mise en place et responsive design.
- MySQL : conformité à l’exigence base relationnelle.
- Script SQL manuel : maîtrise du SQL (pas uniquement via migrations/ORM).

---

# 4. Fonctionnalités implémentées (conformité US)

L’ensemble des User Stories US1 à US13 est pris en charge.

## Visiteur

- Page d’accueil (US1)
- Navigation (US2)
- Recherche + filtres (US3 & US4)
- Détail trajet (US5)
- Création de compte (US7)

## Utilisateur

- Connexion sécurisée
- Gestion crédits
- Participation avec double validation (US6)
- Historique des trajets (US10)

## Chauffeur

- Gestion véhicules (US8)
- Création trajet avec règle des 2 crédits plateforme (US9)
- Démarrage / Fin trajet (US11)
- Signalement incidents

## Employé

- Gestion incidents (US12)
- Validation / refus
- Application des règles métier sur crédits

## Administrateur

- Dashboard
- Statistiques (covoiturages / crédits plateforme) (US13)
- Gestion employés
- Suspension comptes

---

# 5. Base de données

Base relationnelle MySQL.

Script fourni :

ecoride_schema.sql

Le schéma comprend notamment :

- Utilisateur
- Rôle
- Véhicule
- Covoiturage
- Participation
- Avis
- Incident
- Credit_transaction

Les crédits sont gérés via une table de transactions permettant une traçabilité complète.

---

# 6. Sécurité mise en place

- Hash des mots de passe via password_hash()
- Vérification password_verify()
- Requêtes préparées PDO (prévention SQL Injection)
- Échappement HTML (prévention XSS)
- Vérification des rôles côté serveur
- Protection accès routes sensibles
- Sessions sécurisées

---

# 7. Structure du projet

Principaux dossiers :

docs/ → livrables ECF  
public/ → point d’entrée web  
src/ → logique métier (Core / Controllers / Repositories)  
views/ → templates HTML  
config/ → configuration  
ecoride_schema.sql → base de données

---

# 8. Livrables présents dans docs/

- Analyse fonctionnelle
- Charte graphique
- Diagramme de cas d’utilisation
- Diagrammes de séquence (US6, US9)
- Documentation technique
- Documentation gestion de projet
- Manuel utilisateur
- Maquettes (PC & Mobile)
- MCD (Looping)

---

# 9. Installation locale

Prérequis :

- XAMPP
- PHP 8+

Étapes :

1. Copier le projet dans :
   C:\xampp\htdocs\EcoRide

2. Démarrer Apache + MySQL

3. Créer base : ecoride

4. Importer : ecoride_schema.sql

5. Accéder à :
   http://localhost/EcoRide/public/

---

# 10. Compte administrateur

Pseudo : Admin  
Email : admin@ecoride.fr  
Mot de passe : admineco

---

# 11. Limitations et axes d’amélioration

- Pas d’envoi d’email réel (simulation pédagogique)
- Upload photo utilisateur non implémenté
- Améliorations UX possibles
- Améliorations sécurité avancée possibles (CSRF token, rate limiting)

---
