# EcoRide – Projet ECF Développeur Web et Web Mobile

Projet réalisé dans le cadre de l’**Évaluation en Cours de Formation (ECF)** du titre professionnel  
**Développeur Web et Web Mobile (RNCP 37674 – Niveau 5)**.

EcoRide est une plateforme de **covoiturage écologique**, centrée exclusivement sur les déplacements en voiture.

---

## 1) Objectifs du projet

Ce projet a été développé conformément au cahier des charges ECF, incluant notamment :

- Analyse fonctionnelle (User Stories US1 à US13)
- Base de données relationnelle MySQL
- Développement **Front-end** (interfaces web) et **Back-end** (PHP/PDO)
- Gestion des rôles (UTILISATEUR, CHAUFFEUR, EMPLOYE, ADMINISTRATEUR)
- Sécurité (sessions, hash mots de passe, requêtes préparées, échappement HTML)
- Documentation et livrables (maquettes, charte graphique, manuel utilisateur, etc.)

---

## 2) Fonctionnalités implémentées

### Visiteur

- Accès à la page d’accueil
- Recherche de covoiturages (ville départ, ville arrivée, date + filtres)
- Consultation de la liste des covoiturages disponibles
- Consultation du détail d’un covoiturage
- Accès aux pages Contact et Mentions légales
- Création de compte utilisateur

### Utilisateur

- Connexion / déconnexion
- Accès à l’espace personnel
- Consultation du solde de crédits
- Participation à un covoiturage
- Consultation de l’historique des trajets

### Chauffeur

- Gestion des véhicules
- Création de covoiturages
- Consultation / gestion de ses trajets (statuts)
- Démarrage / fin de trajet
- Signalement d’un incident

### Employé

- Consultation des trajets signalés en incident
- Validation ou refus d’un trajet en incident
- Application des règles métier associées aux crédits (validation = crédit chauffeur)

### Administrateur

- Tableau de bord
- Statistiques (covoiturages par jour, crédits gagnés par la plateforme)
- Gestion des comptes employés
- Suspension / réactivation de comptes utilisateurs

> ✅ Les User Stories **US1 à US13** sont prises en charge.  
> ⚠️ Certaines parties ont été simplifiées par manque de temps (voir “Limitations connues”).

---

## 3) Gestion des crédits (résumé)

- 20 crédits offerts à l’inscription
- Débit automatique lors d’une participation
- Crédit chauffeur après validation d’un trajet
- Historique des transactions enregistré en base

---

## 4) Stack technique

### Front-end

- HTML5
- CSS3 (Bootstrap + CSS personnalisé)

### Back-end

- PHP 8
- PDO (requêtes préparées)
- Architecture MVC maison (sans framework)
- Routeur maison (GET/POST)

### Base de données

- MySQL
- Script SQL officiel : `ecoride_schema.sql`

---

## 5) Livrables / Documentation (dossier docs)

Tous les documents demandés pour l’ECF sont versionnés dans le dépôt GitHub, dans le dossier `docs/` :

- **Charte graphique** : `docs/charte_graphique/Charte_graphique_EcoRide.pdf`
- **Analyse fonctionnelle** : `docs/analyse_fonctionnelle/Analyse_fonctionnelle_EcoRide.docx` (et/ou `.pdf` si exportée)
- **Manuel utilisateur** : `docs/manuel_utilisateur/Manuel_utilisateur_EcoRide.docx` (et/ou `.pdf` si exporté)
- **Maquettage** (8 PNG) : `docs/maquettes/`
  - 1.  Bureau Accueil.png
  - 2.  Bureau liste covoiturages.png
  - 3.  Bureau Détail covoiturage.png
  - 4.  Bureau login.png
  - 5.  Bureau register.png
  - 6.  Mobile Accueil.png
  - 7.  Mobile Liste Covoiturages.png
  - 8.  Mobile Detail Covoiturage.png

---

## 6) Base de données

### Script SQL officiel

Le script de création et d’initialisation de la base est fourni à la racine du projet :

- `ecoride_schema.sql`

### Compte administrateur par défaut (créé via le script SQL)

- **Pseudo** : Admin
- **Email** : admin@ecoride.fr
- **Mot de passe** : admineco

---

## 7) Installation locale (XAMPP)

### Prérequis

- XAMPP (Apache + MySQL)
- PHP 8+
- Navigateur web moderne

### Étapes

1. Copier/cloner le projet dans :
   ```
   C:\xampp\htdocs\EcoRide
   ```
2. Démarrer **Apache** et **MySQL** depuis XAMPP
3. Créer une base MySQL nommée `ecoride`
4. Importer le fichier `ecoride_schema.sql` (via phpMyAdmin)
5. Accéder à l’application :
   ```
   http://localhost/EcoRide/public/
   ```

---

## 8) Déploiement

Le déploiement n’est pas encore effectué à ce stade.  
Une fois réalisé, la section sera mise à jour avec l’URL de l’application déployée.

---

## 9) Limitations connues

Dans le cadre du temps imparti :

- Les emails utilisés sont fictifs (pas d’envoi réel)
- Gestion des photos utilisateur non implémentée
- Affichage détaillé des préférences chauffeur et des avis partiellement implémenté
- Optimisation mobile perfectible malgré une base responsive Bootstrap
- Logo prévu dans les maquettes non intégré

---
