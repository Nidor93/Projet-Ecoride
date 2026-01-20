<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: connexion.php');
    exit();
}

if (isset($_GET['suspendre_id'])) {
    $id_a_modifier = intval($_GET['suspendre_id']);
    
    $pdo->prepare("UPDATE utilisateur SET est_suspendu = 1 - est_suspendu WHERE utilisateur_id = ?")
        ->execute([$id_a_modifier]);
    
    header('Location: profil_admin.php?statut=success');
    exit();
}

$stmt_total = $pdo->query("SELECT SUM(commission_credit) as total FROM reservation");
$total_credits = $stmt_total->fetch()['total'] ?? 0;

$graph1_query = $pdo->query("SELECT date_depart, COUNT(*) as nb_trajets FROM trajet GROUP BY date_depart ORDER BY date_depart ASC LIMIT 7");
$dates = []; $nb_trajets = [];
while($row = $graph1_query->fetch()) {
    $dates[] = $row['date_depart'];
    $nb_trajets[] = $row['nb_trajets'];
}

$graph2_query = $pdo->query("
    SELECT t.date_depart, SUM(r.commission_credit) as gain 
    FROM reservation r
    JOIN trajet t ON r.trajet_id = t.trajet_id 
    GROUP BY t.date_depart 
    ORDER BY t.date_depart ASC 
    LIMIT 7
");
$gains = [];
while($row = $graph2_query->fetch()) {
    $gains[] = $row['gain'];
}

$users = $pdo->query("SELECT * FROM utilisateur WHERE role != 'admin' ORDER BY role DESC")->fetchAll();

$queryTrajets = $pdo->query("SELECT date_depart, COUNT(*) as total FROM trajet GROUP BY date_depart ORDER BY date_depart ASC LIMIT 10");
$statsTrajets = $queryTrajets->fetchAll(PDO::FETCH_ASSOC);

// STAT 2 : Répartition Électrique vs Thermique (pour un graphique en camembert)
$queryEco = $pdo->query("SELECT 
    SUM(CASE WHEN est_electrique = 1 THEN 1 ELSE 0 END) as electrique,
    SUM(CASE WHEN est_electrique = 0 THEN 1 ELSE 0 END) as thermique
    FROM voiture");
$statsEco = $queryEco->fetch(PDO::FETCH_ASSOC);

// Conversion en JSON pour JavaScript
$jsonTrajets = json_encode($statsTrajets);
$jsonEco = json_encode($statsEco);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Administrateur - Modération</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">EcoRide</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active fw-bold" href="profil_admin.php">Administrateur</a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="main-content container my-4">
    <div class="card border-0 shadow-sm mb-4">
        <h3 class="col-md-4 text-center fw-bold text-success">Créer un compte employé</h3>
        <div class="card-body">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Le compte employé a été créé avec succès !</div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo ($_GET['error'] === 'email_existant') ? "Cet email est déjà utilisé." : "Une erreur est survenue lors de la création.";
                    ?>
                </div>
            <?php endif; ?>
            <form action="creer_employe.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Nom</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Prénom</label>
                        <input type="text" name="prenom" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="nomprenom@mail.com" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Mot de passe</label>
                        <input type="password" name="password" class="form-control" id="password" required minlength="12" pattern=".*[^\w\s].*" placeholder="Minimum 12 caractères">
                    </div>
                    <div class="col-12 mt-6">
                        <button type="submit" class="btn btn-success">Créer l'employé</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card border-0 shadow-sm p-4 mb-2">
        <div class="row mb-4">
            <div class="col-md-4 text-center">
                <div class="card bg-success text-white p-4">
                    <h3>Total Crédits Gagnés</h3>
                    <h1 class="display-4 fw-bold"><?php echo number_format($total_credits, 0, '.', ' '); ?></h1>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6"><canvas id="chartTrajets"></canvas></div>
                    <div class="col-md-6"><canvas id="chartGains"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    fetch('api_stats.php')
        .then(response => response.json())
        .then(stats => {
            new Chart(document.getElementById('chartTrajets'), {
                type: 'line',
                data: {
                    labels: stats.labels,
                    datasets: [{ 
                        label: 'Covoiturages / jour', 
                        data: stats.trajets, 
                        borderColor: 'green',
                        backgroundColor: 'rgba(0, 128, 0, 0.1)',
                        fill: true
                    }]
                }
            });

            new Chart(document.getElementById('chartGains'), {
                type: 'bar',
                data: {
                    labels: stats.labels,
                    datasets: [{ 
                        label: 'Crédits gagnés / jour', 
                        data: stats.gains, 
                        backgroundColor: 'green' 
                    }]
                }
            });
        })
        .catch(error => console.error('Erreur lors du chargement des stats:', error));
    </script>

    <div class="card border-0 shadow-sm p-2 mb-6">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $u): ?>
            <tr>
                <td><?php echo $u['prenom'].' '.$u['nom']; ?></td>
                <td><span class="badge bg-info text-dark"><?php echo $u['role']; ?></span></td>
                <td>
                    <?php echo ($u['est_suspendu']) ? '<span class="text-danger">Suspendu</span>' : '<span class="text-success">Actif</span>'; ?>
                </td>
                <td>
                    <a href="profil_admin.php?suspendre_id=<?php echo $u['utilisateur_id']; ?>" 
                       class="btn btn-sm <?php echo ($u['est_suspendu']) ? 'btn-success' : 'btn-danger'; ?>">
                       <?php echo ($u['est_suspendu']) ? 'Réactiver' : 'Suspendre'; ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<footer class="bg-success text-white text-center py-3 mt-auto">
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>