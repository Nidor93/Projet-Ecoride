Présentation du projet :

EcoRide est une application web de covoiturage écologique permettant de mettre en relation des conducteurs et des passagers pour des trajets en voiture, avec une mise en avant des véhicules électriques.
Ce projet est réalisé dans le cadre de l’ECF du titre professionnel Développeur Web et Web Mobile.

Objectifs :

Réduire l’impact environnemental des déplacements
Proposer une plateforme simple et sécurisée de covoiturage
Mettre en œuvre une application web complète (front, back, bases de données, déploiement)

Stack technique :
Front-end
HTML5
CSS3 + Bootstrap
JavaScript (vanilla)

Back-end
PHP (architecture MVC)
PDO pour l’accès aux données

Bases de données
MySQL (données relationnelles : utilisateurs, trajets, crédits…)
MongoDB (statistiques, logs, données analytiques)

Déploiement
Application : Fly.io / Heroku
Base NoSQL : MongoDB Atlas

Fonctionnalités principales :

Recherche et filtrage de covoiturages
Inscription et authentification sécurisée
Gestion des rôles (visiteur, utilisateur, chauffeur, employé, administrateur)
Gestion des crédits
Création et participation à des trajets
Avis et notation des chauffeurs
Statistiques administrateur

Sécurité :

Hashage des mots de passe (password_hash)
Requêtes préparées (PDO)
Gestion des sessions
Contrôle des accès par rôles
Validation des données côté serveur

Installation en local :

Cloner le dépôt GitHub
Configurer le fichier .env
Importer la base de données SQL
Lancer le serveur local (Apache / PHP)
Vérifier la connexion à MongoDB

Organisation du projet :
/public
/app
  /controllers
  /models
  /services
/config
/routes

Livrables :

Dépôt GitHub public
Application déployée
Kanban de gestion de projet
Documentation technique
Manuel utilisateur
Charte graphique


Projet réalisé par : Ferré Mathis

Licence : Projet pédagogique – Studi