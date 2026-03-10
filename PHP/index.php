<?php
session_start();
require_once '../db_connect.php';

$queryEco = $pdo->query("SELECT COUNT(*) FROM voiture WHERE est_electrique = 1");
$nbEco = $queryEco->fetchColumn();
?>
<?php include('../components/header.php') ?>

<body class="d-flex flex-column min-vh-100">

<?php include('../components/nav.php') ?>

<section class="hero text-center py-5">
    <div class="container">
        <h1 class="display-5 fw-bold">Voyagez autrement avec EcoRide</h1>
        <p class="lead mt-3">
            Le covoiturage écologique pour réduire votre impact environnemental.
        </p>

        <form action="../PHP/recherche.php" method="GET" class="row g-3 justify-content-center mt-4">
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
<section class="section">
    <div class="row text-center">
        <section class="container2">
            <div class="col-md-14 g-6">
                <div class="eco-icon"><img src="../Image/VoitureEcoRide.png" alt="Voiture EcoRide" class ="Image"></div>
                <h4 class="mt-3">Écologique</h4>
                <p>Prioriter aux trajets en voiture électrique et responsable.</p>
            </div>
        </section>
        <section class="container2">
            <div class="col-md-14 g-6">
                <div class="eco-icon"><img src="../Image/Electricite-verte.png" alt="Ampoule représentant l'energie verte" class ="Image"></div>
                <h4 class="mt-3">Economique</h4>
                <p>Partagez les frais et voyagez à moindre coût.</p>
            </div>
        </section>
        <section class="container2">
            <div class="col-md-14 g-6">
                <div class="eco-icon"><img src="../Image/Shakehands.png" alt="Poignet de main" class ="Image"></div>
                <h4 class="mt-3">Convivial</h4>
                <p>Rencontrez des voyageurs partageant les mêmes valeurs.</p>
            </div>
        </section>
    </div>
</section>
<?php include("../components/footer.html"); ?>

</body>