<?php
session_start();
require_once '../db_connect.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM reservation");
$nb_reservations = $stmt->fetchColumn();

$co2_economise = $nb_reservations * 5;
?>

<?php include('../components/header.php') ?>

<body class="d-flex flex-column min-vh-100">

<?php include('../components/nav.php') ?>

<section class="register-background py-5">
    <div class="container">
        <div class="logregis-card mx-auto p-4 bg-grey shadow rounded" style="max-width: 600px;">
            <div class="text-center mb-4">
                <i class="bi bi-tree-fill text-success" style="font-size: 3rem;"></i>
                <h2 class="fw-bold text-success mt-2">Impact Écologique EcoRide</h2>
                <hr class="mx-auto w-25">
            </div>

            <div class="py-4 text-center">
                <h1 class="display-1 fw-bold text-dark mb-0 counter-value">
                    <?= number_format($co2_economise, 1, ',', ' '); ?>
                </h1>
                <p class="h4 text-muted">Kilogrammes de Co2 éviter</p>
            </div>

            <div class="row mt-4 text-center">
                <div class="col-md-6 border-end">
                    <h5 class="fw-bold"><?= $nb_reservations ?></h5>
                    <p>Covoiturages réaliser</p>
                </div>
                <div class="col-md-6">
                    <h5 class="fw-bold"><?= round($co2_economise / 20, 1) ?></h5>
                    <p>Équivalent arbres planter</p>
                </div>
                <div class="alert alert-success mt-4 mb-2">
                        <strong>Le saviez-vous ?</strong> En partageant votre véhicule, vous divisez par 2 ou 3 votre empreinte carbone sur chaque trajet.
                </div>
        
                <div class="text-center mt-4">
                    <a href="recherche.php" class="btn btn-outline-success">Contribuer à préserver notre planete</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('../components/footer.html') ?>

</body>

