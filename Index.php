<?php
session_start();
require_once 'db_connect.php';

$queryEco = $pdo->query("SELECT COUNT(*) FROM voiture WHERE est_electrique = 1");
$nbEco = $queryEco->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EcoRide - Covoiturage écologique</title>
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
               <li class="nav-item"><a class="nav-link active fw-bold" href="index.php">Accueil</a></li>
               <li class="nav-item"><a class="nav-link" href="recherche.php">Accès aux Covoiturages</a></li>
            <?php if (isset($_SESSION['utilisateur_id'])): ?>
               <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
               <li class="nav-item"><a class="nav-link text-warning" href="deconnexion.php">Déconnexion</a></li>
            <?php else: ?>
               <li class="nav-item"><a class="nav-link" href="connexion.php">Connexion</a></li>
               <li class="nav-item"><a class="nav-link" href="inscription.php">Inscription</a></li>
            <?php endif; ?>
               <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero text-center py-5">
    <div class="container">
        <h1 class="display-5 fw-bold">Voyagez autrement avec EcoRide</h1>
        <p class="lead mt-3">
            Le covoiturage écologique pour réduire votre impact environnemental.
        </p>

        <form action="recherche.php" method="GET" class="row g-3 justify-content-center mt-4">
            <div class="col-md-4">
               <input type="text" name="depart" class="form-control" placeholder="Ville de départ" required>
            </div>
            <div class="col-md-4">
               <input type="text" name="arrivee" class="form-control" placeholder="Ville d’arrivée" required>
            </div>
            <div class="col-md-2 d-grid">
               <button type="submit" class="btn btn-light fw-bold text-success">
                 Rechercher
               </button>
            </div>
        </form>
    </div>
</section>

<section class="container2">
    <div class="row text-center">
        <div class="col-md-4">
            <div class="eco-icon"><img src="./Image/Voiture EcoRide.png" alt="Voiture EcoRide" class ="Image"></div>
            <h4 class="mt-3">Écologique</h4>
            <p>Prioriter aux trajets en voiture électrique et responsable.</p>
        </div>
        <div class="col-md-4">
            <div class="eco-icon"><img src="./Image/Electricite-verte.png" alt="Ampoule représentant l'energie verte" class ="Image"></div>
            <h4 class="mt-3">Economique</h4>
            <p>Partagez les frais et voyagez à moindre coût.</p>
        </div>
        <div class="col-md-4">
            <div class="eco-icon"><img src="./Image/Shakehands.png" alt="Poignet de main" class ="Image"></div>
            <h4 class="mt-3">Convivial</h4>
            <p>Rencontrez des voyageurs partageant les mêmes valeurs.</p>
        </div>
    </div>
</section>

<footer class="bg-success text-white text-center py-3 mt-auto">
    <p class="mb-1">contact@ecoride.fr</p>
    <a href="mentions-legales.php" class="text-white text-decoration-underline">Mentions légales</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>