<?php
session_start();
require_once '../db_connect.php';

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

$graph2_query = $pdo->query("SELECT t.date_depart, SUM(r.commission_credit) as gain 
                             FROM reservation r
                             JOIN trajet t ON r.trajet_id = t.trajet_id 
                             GROUP BY t.date_depart 
                             ORDER BY t.date_depart ASC 
                             LIMIT 7");
$gains = [];
while($row = $graph2_query->fetch()) {
    $gains[] = $row['gain'];
}

$search = isset($_GET['nom']) ? trim($_GET['nom']) : '';

if ($search !== '') {
    $stmt_users = $pdo->prepare("SELECT * FROM utilisateur WHERE role != 'admin' AND (prenom LIKE ? OR nom LIKE ?) ORDER BY role DESC");
    $stmt_users->execute(["%$search%", "%$search%"]);
} else {
    $stmt_users = $pdo->query("SELECT * FROM utilisateur WHERE role != 'admin' ORDER BY role DESC");
}
$users = $stmt_users->fetchAll();

$queryTrajets = $pdo->query("SELECT date_depart, COUNT(*) as total FROM trajet GROUP BY date_depart ORDER BY date_depart ASC LIMIT 10");
$statsTrajets = $queryTrajets->fetchAll(PDO::FETCH_ASSOC);

$queryEco = $pdo->query("SELECT 
    SUM(CASE WHEN est_electrique = 1 THEN 1 ELSE 0 END) as electrique,
    SUM(CASE WHEN est_electrique = 0 THEN 1 ELSE 0 END) as thermique
    FROM voiture");
$statsEco = $queryEco->fetch(PDO::FETCH_ASSOC);

$jsonTrajets = json_encode($statsTrajets);
$jsonEco = json_encode($statsEco);

$stmt_incidents = $pdo->prepare("SELECT a.note, a.commentaire, a.trajet_id, t.ville_depart, t.ville_arrivee, t.date_depart, t.heure_depart, u.prenom as passager_nom
                                 FROM avis a
                                 JOIN trajet t ON a.trajet_id = t.trajet_id
                                 JOIN utilisateur u ON a.passager_id = u.utilisateur_id
                                 WHERE a.note <= 2
                                 ORDER BY t.date_depart DESC");
$stmt_incidents->execute();
$incidents = $stmt_incidents->fetchAll();

?>
<?php include('../components/header.php') ?>

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

<div class="main-content container py-4">
    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4 mb-4 sticky-top" style="top: 20px;">
                <h3 class="fw-bold text-success mb-4">Tableau de Bord</h3>
                <div class="d-grid gap-3">
                    <button class="btn btn-success fw-bold shadow-sm" data-bs-toggle="collapse" data-bs-target="#collapseCree">
                        <i class="bi bi-person-plus"></i> Créer un employé
                    </button>
                    <button class="btn btn-success fw-bold shadow-sm" data-bs-toggle="collapse" data-bs-target="#collapseStats">
                        <i class="bi bi-graph-up"></i> Statistiques du site
                    </button>
                    <button class="btn btn-success fw-bold shadow-sm" data-bs-toggle="collapse" data-bs-target="#collapseUtilisateurs">
                        <i class="bi bi-shield-lock"></i> Gérer les utilisateurs
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            
            <div class="collapse mb-4" id="collapseCree" data-bs-parent=".main-content">
                <div class="card border-0 shadow-sm p-4">
                    <h3 class="fw-bold text-success text-center mb-4">Créer un compte employé</h3>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">Le compte employé a été créé avec succès !</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?= ($_GET['error'] === 'email_existant') ? "Cet email est déjà utilisé." : "Une erreur est survenue lors de la création."; ?>
                        </div>
                    <?php endif; ?>
                    <?php include("../form/creer_employe_form.html"); ?>
                </div>
            </div>

            <div class="collapse show mb-4" id="collapseStats" data-bs-parent=".main-content">
                <div class="card border-0 shadow-sm p-4">
                    <h3 class="fw-bold text-success text-center mb-4">Statistiques Globales</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-12 text-center">
                            <div class="card bg-success text-white p-3 border-0">
                                <h3>Total Crédits Gagnés</h3>
                                <h1 class="display-4 fw-bold"><?= number_format($total_credits, 0, '.', ' '); ?></h1>
                            </div>
                        </div>
                        <div class="col-md-6"><canvas id="chartTrajets"></canvas></div>
                        <div class="col-md-6"><canvas id="chartGains"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="collapse mb-4" id="collapseUtilisateurs" data-bs-parent=".main-content">
                <div class="card border-0 shadow-sm p-4">
                    <h3 class="fw-bold text-danger text-center mb-4">Modération Utilisateurs</h3>
                    <?php include('../components/litige_card.php') ?>
                    
                    <form action="profil_admin.php" method="GET" class="row g-2 mb-4 mt-2">
                        <div class="col-md-9">
                            <input type="text" name="nom" class="form-control" placeholder="Rechercher un nom ou prénom..." value="<?= htmlspecialchars($search ?? ''); ?>">
                        </div>
                        <div class="col-md-3 d-grid">
                            <button type="submit" class="btn btn-success fw-bold">Rechercher</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr><td colspan="4" class="text-center py-4">Aucun utilisateur trouvé.</td></tr>
                                <?php else: ?>
                                    <?php foreach($users as $u): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($u['prenom'].' '.$u['nom']); ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($u['email']); ?></small>
                                        </td>
                                        <td><span class="badge bg-info text-dark"><?= ucfirst($u['role']); ?></span></td>
                                        <td><?= ($u['est_suspendu']) ? '<span class="badge bg-danger">Suspendu</span>' : '<span class="badge bg-success">Actif</span>'; ?></td>
                                        <td>
                                            <a href="profil_admin.php?suspendre_id=<?= $u['utilisateur_id']; ?>" class="btn btn-sm <?= ($u['est_suspendu']) ? 'btn-success' : 'btn-danger'; ?>">
                                                <?= ($u['est_suspendu']) ? 'Réactiver' : 'Suspendre'; ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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

<footer class="bg-success text-white text-center py-3 mt-auto">
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>