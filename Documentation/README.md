Résumé du projet :

EcoRide est une startup de covoiturage écologique permettant de mettre en relation des conducteurs et des passagers pour des trajets en voiture, avec une mise en avant des véhicules électriques.
Ce projet est réalisé dans le cadre de l’ECF du titre professionnel développeur web et web mobile.
José à pour objectifs avec ce site de permettre de réduire l’impact environnemental des déplacements en proposant une plateforme simple et sécurisée de covoiturage qui favorise les transports écologique.

Pour cela il me demande de mettre en œuvre une application web complète (front, back, bases de données, déploiement).
C'est à dire que conformément au cahier des charges fourni par José les pages du site web sont stylisé afin de suscité l'écologie et la modernité chez l'utilisateur. 

D'autre part la sécurité est primordiale afin d'assurer le bon fonctionnement de l'application web pour les utilisateurs.
La plateforme est modérer par les employés qui peuvent valider les avis et contacté les utilisateurs en cas de problèmes lors d'une course.
Quand aux administrateurs, ils ont la capacité de suspendre un compte en cas de non respects des règles du site web.

De plus un espace administratif est conçu de manière de permettre a la startup de consulté leurs bénéfices de leur site et le nombre de covoiturage effectuer sur une certaine durée.

Objectifs :

Réduire l’impact environnemental des déplacements
Proposer une plateforme simple et sécurisée de covoiturage
Mettre en œuvre une application web complète (front, back, bases de données, déploiement)



Explication de la gestion de projet : 

Méthodologie Kanban :
Pour ce projet, j’ai adopté la méthodologie Kanban.
Ce choix a été motivé par la nécessité d'avoir une vision claire des fonctionnalités à développer en ayant un tableau simplement organiser.
Le Kanban permet de visualiser le flux de travail et d'identifier immédiatement les blocages.

Organisation du tableau de bord :
Mon tableau de bord réalisé sur Trello respecte les 5 colonnes demandées dans l'énoncé pour assurer un bon suivi :

Fonctionnalités prévues (Backlog) : Cette colonne contient l'ensemble des User Stories (US) extraites du cahier des charges, triées par importance. J'ai placé les fonctions critiques (recherche, création de compte) en haut de liste.
A faire (Sprint) : les taches que j'ai décidé de traiter pour la session de travail actuelle (ex: Maquettage et Setup BDD).
En cours (In Progress) : la fonctionnalité sur laquelle je travaille activement. Pour garantir la qualité, je m'impose de ne pas avoir plus de deux tâches simultanés ici.
Terminé (Branche développement) : fonctionnalités coder et tester localement, puis fusionner sur la branche develop.
Merge (Branche principale) : fonctionnalités valider et fusionner sur la branche main, constituant la version stable et final du site.

Lien du gestion de projet : https://trello.com/invite/b/696e073f3b1e1ae83615e77b/ATTIacdaead3476a15092da66e4df2762b602418D8DB/gestion-de-projet

Gestion du versionnage avec Git :
La gestion de projet est couplée à une stratégie de branches stricte (Git Flow) pour éviter les régressions et garantir la sécurité du code.
Isolement : chaque nouvelle fonctionnalité est développée sur une branche isolée.
Intégration : une fois la fonction coder, elle est fusionnée dans la branche development pour les tests d'intégration.
Livraison : le passage vers la branche main ne se fait qu'une fois la fonctionnalité totalement stable.

Priorisation des fonctionnalités :
J'ai découpé le projet en trois phases :
Phase 1 (Fondations) : installation de l'environnement, création de la base de données et page d'accueil.
Phase 2 (Cœur de métier) : système de recherche de trajet, création de compte et gestion des crédits (US 1 à 11).
Phase 3 (Administration) : modération des avis et espace administrateur (US 12, 13).

Recherche et Auto-formation :
La gestion de projet a également nécessité des phases de recherche. Pour l'implémentation de la sécurité (hachage des mots de passe) et des fonctionnalités spécifiques (filtre).
J'ai consulté des documentations techniques anglophones (ex: PHP.net, tutoriels YouTube, blog). 
Cela m'a permis d'intégrer des solutions modernes et sécurisées conformément aux attentes du client.

Stack technique :
Front-end
HTML5
CSS3 + Bootstrap
JavaScript (vanilla)

Back-end :
PHP (architecture MVC)
PDO pour l’accès aux données

Bases de données :
MySQL (données relationnelles : utilisateurs, trajets, crédits…)
MongoDB (statistiques, logs, données analytiques)

Déploiement :
Application : Fly.io
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

Manuel d'utilisation en local :
Cloner le dépôt GitHub
Verifier si php est installer sur le systeme
Ouvrir le dossier dans Vs code
Importer la base de données ecoride_db.sql dans votre BDD
Lancer le serveur local (Apache / PHP) avec XAMPP
Vérifier la connexion à MongoDB
Lancer le serveur grace à l'extension PHP server project sur Vs code (clic droit sur Index.php)

Utilisateur mail et mot de passe : test@mail.com azertyuiop/1
Admin mail et mot de passe : admin@mail.com  Azertyuiop/1
Employe mail et mot de passe : employe@mail.com  Azertyuiop/1



Configuration de l'envirronement : 

Pile technologique (Stack LAMP) :
Le projet repose sur une architecture robuste et éprouvée pour le Web dynamique :
Serveur web : apache (inclus dans XAMPP/WAMP).
Langage Backend : php pour la logique métier et la communication avec la base de données.
Système de gestion de base de données : mysql pour le stockage relationnel (utilisateurs, trajets, réservation).
Frontend : HTML5, CSS3 et JavaScript (vanilla ou via bibliothèques comme Chart.js pour les statistiques admin).

Gestion de la Base de Données :
Pour garantir l'intégrité et la portabilité des données :
Outil de gestion : phpMyAdmin ou un client sql.
Sécurité : les accès à la base de données (host, user, password) doivent être externalisés dans un fichier de configuration (ex: db_connect.php).

Gestion du code source (Git & GitHub) :
Versionnage : utilisation de Git pour suivre l'historique des modifications.
Hébergement : GitHub pour le dépôt distant.

Outils de développement utiliser :
Visual studio code (avec extensions PHP Server et Prettier pour le formatage).
Utilisation du navigateurs Chrome avec les outils de développement (F12 ou inspect) pour déboguer le réseau et le DOM.



Réflexion technologique : 

Architecture globale :
Le projet repose sur une architecture client serveur classique, utilisant le stack LAMP (linux, apache, mysql, php). En effet cela rend le site plus accessible et interactif.
Cela permet aux utilisateurs d'accéder à Ecoride depuis n'importe quel appareil (PC, tablette, téléphone) sans installation.
L'utilisation de php permet de générer des pages dynamiques (pour la recherche de trajets par exemple) en communiquant en temps réel avec la base de données.

Stratégie de gestion des données (mysql) :
La réflexion s'articule autour de trois axes :
Intégrité référentielle : grace aux clés étrangères (comme trajet_id) cela garantie qu'une reservation ne peut pas exister sans un trajet existant.
Sécurité : les mots de passe sont hacher (grace à password_hash en php) avant d'etre stocker, conformément aux recommandations de la CNIL.
Evolutivité : la structure en tables séparer (Utilisateur, Voiture, Trajet, Avis) permet d'ajouter des futures fonctionnalités (comme un système de paiement) sans devoir refaire tout le système derrière.

Séparation des roles et securité :
L'implémentation de la hiérarchie utilisateur < employe < administrateur figure dans le cahier des charges.
Controle d'accès : chaque page sensible vérifie le rôle en session. Par exemple un employe ne peut pas accéder à profil_admin.php.
Par exemple la modération avec le système de validation (est_valide : tinyint) assure que seul les contenus vérifier peuvent modifier la réputation des conducteurs.

Statistiques et échanges de données :
Pour le pilotage de l'entreprise, Ecoride utilise des technologies modernes d'échange :
API interne & JSON : l'administrateur consulte des statistiques générer en JSON. Ce format est léger, universel et permet de séparer la logique de calcul (php) de la logique d'affichage.
L'intégration de librairies comme Chart.js transforme ces données brutes en graphiques lisibles, pour une lecture et compréhension plus simple pour l'administrateur.

Livrables :

Dépôt GitHub public
Application déployée
Lien de gestion de projet
Documentation technique
Manuel utilisateur
Charte graphique


Lien deployer : https://ecoride-mat.alwaysdata.net
Lien du dépot github : https://github.com/Nidor93/Projet-Ecoride.git
Lien anglophone : https://youtu.be/aUW5GAFhu6s?si=lc3GcGBGrVO87Scg

Projet réalisé par : Ferré Mathis

Licence : Projet pédagogique – Studi