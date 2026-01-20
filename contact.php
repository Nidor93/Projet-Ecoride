<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EcoRide - Contact</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">EcoRide</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
               <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
               <li class="nav-item"><a class="nav-link" href="recherche.php">Accès aux Covoiturages</a></li>
            <?php if (isset($_SESSION['utilisateur_id'])): ?>
               <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
               <li class="nav-item"><a class="nav-link text-warning" href="deconnexion.php">Déconnexion</a></li>
            <?php else: ?>
               <li class="nav-item"><a class="nav-link" href="connexion.php">Connexion</a></li>
               <li class="nav-item"><a class="nav-link" href="inscription.php">Inscription</a></li>
            <?php endif; ?>
               <li class="nav-item"><a class="nav-link active fw-bold" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="bg-light py-5 text-center border-bottom">
    <div class="container">
        <h1 class="display-4 fw-bold text-success">Informations à propos de Ecoride</h1>
        <p class="lead text-muted">Transparence et engagement pour un covoiturage responsable.</p>
    </div>
</header>

<main class="container my-5">
    <div class="bg-white p-4 p-md-5 shadow-sm rounded">
        <p class="text-muted">Dernière mise à jour : 20 Décembre 2025</p>
        <hr>

        <section class="mb-4">
            <h2 class="h4 fw-bold">1. Présentation du service</h2>
            <p>La plateforme <strong>EcoRide</strong> a pour objectif de réduire l'impact environnemental des déplacements en encourageant le covoiturage exclusivement par véhicules motorisés. Le service met en relation des chauffeurs et des passagers partageant les mêmes valeurs écologiques.</p>
        </section>

        <section class="mb-4">
            <h2 class="h4 fw-bold">2. Système de Crédits</h2>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Dotation initiale :</strong> À la création d'un compte, chaque utilisateur bénéficie de <span class="badge bg-success">20 crédits offerts</span></li>
                <li class="list-group-item"><strong>Frais de plateforme :</strong> Pour chaque trajet, la plateforme prélève <span class="text-danger fw-bold">2 crédits</span> pour garantir son bon fonctionnement.</li>
                <li class="list-group-item"><strong>Validation :</strong> Les crédits sont transférés au chauffeur une fois que le passager a validé le bon déroulement du trajet via son espace personnel.</li>
            </ul>
        </section>

        <section class="mb-4">
            <h2 class="h4 fw-bold">3. Engagements du Chauffeur</h2>
            <p>Tout utilisateur souhaitant proposer un trajet s'engage à :</p>
            <ul>
                <li>Fournir des informations exactes (plaque d'immatriculation, modèle, couleur)</li>
                <li>Déclarer ses préférences (fumeur/non-fumeur, animaux acceptés ou non)</li>
                <li>Maintenir le nombre de places disponibles déclaré</li>
                <li>Mentionner si le voyage est <strong>écologique</strong> (utilisation d'un véhicule électrique)</li>
            </ul>
        </section>

        <section class="mb-4">
            <h2 class="h4 fw-bold">4. Annulation et Litiges</h2>
            <p>En cas d'annulation par le chauffeur, un mail est envoyé aux participants et les crédits sont remboursés. En cas de litige signalé par un participant, un employé EcoRide interviendra pour résoudre la situation avant toute mise à jour des crédits.</p>
        </section>

        <section class="mb-4">
            <h2 class="h4 fw-bold">5. Modération et Avis</h2>
            <p>Les avis et notes attribués aux chauffeurs sont soumis à une validation préalable par un employé EcoRide avant d'etre publié sur la plateforme.</p>
        </section>

        <div class="text-center mt-5">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="profil.php" class="btn btn-outline-success btn-lg px-5">Retour à mon profil</a>
            <?php else: ?>
                <a href="inscription.php" class="btn btn-success btn-lg px-5" style="border-radius: 15px;">Inscrivez vous</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer class="bg-success text-white text-center py-3 mt-auto">
    <p class="mb-1">contact@ecoride.fr</p>
    <a href="mentions-legales.php" class="text-white text-decoration-underline">Mentions légales</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>