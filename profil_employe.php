<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employe') {
    header('Location: connexion.php');
    exit();
}

$stmt = $pdo->prepare("SELECT role FROM utilisateur WHERE utilisateur_id = ?");
$stmt->execute([$_SESSION['utilisateur_id']]);
$user = $stmt->fetch();

if (!$user || trim($user['role']) !== 'employe') {
    header('Location: profil.php?error=not_authorized');
    exit;
}

$stmt_avis = $pdo->prepare("
    SELECT 
        a.*, 
        u.prenom as passager_nom, 
        u.email as passager_email, 
        c.prenom as chauffeur_nom, 
        c.email as chauffeur_email 
    FROM avis a 
    JOIN utilisateur u ON a.passager_id = u.utilisateur_id 
    JOIN utilisateur c ON a.utilisateur_id = c.utilisateur_id 
    WHERE a.est_valide = 0
");
$stmt_avis->execute();
$avis_en_attente = $stmt_avis->fetchAll();

$stmt_incidents = $pdo->prepare("
    SELECT 
        a.note, a.commentaire, a.trajet_id,
        t.ville_depart, t.ville_arrivee, t.date_depart, t.heure_depart,
        u.prenom as passager_nom
    FROM avis a
    JOIN trajet t ON a.trajet_id = t.trajet_id
    JOIN utilisateur u ON a.passager_id = u.utilisateur_id
    WHERE a.note <= 2
    ORDER BY t.date_depart DESC
");
$stmt_incidents->execute();
$incidents = $stmt_incidents->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Employé - Modération</title>
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
                <li class="nav-item"><a class="nav-link active fw-bold" href="profil_employe.php">Employe</a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>


<div class="container mt-5">

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <?php 
                if($_GET['msg'] == 'valide') echo "L'avis a été publié.";
                if($_GET['msg'] == 'refuse') echo "L'avis a été supprimé.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if (count($avis_en_attente) > 0): ?>
            <?php foreach ($avis_en_attente as $avis): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title text-primary">
                                    De : 
                                    <?php echo htmlspecialchars($avis['passager_nom']); ?> <small class="text-muted">(<?php echo htmlspecialchars($avis['passager_email']); ?>)</small>
                                </h5>
                                <span class="badge bg-warning text-dark"><?php echo $avis['note']; ?> / 5 ★</span>
                            </div>
                            <h6 class="card-subtitle mb-2 text-muted small">
                            Pour le chauffeur : 
                            <strong><?php echo htmlspecialchars($avis['chauffeur_nom']); ?></strong> <span class="text-muted">(<?php echo htmlspecialchars($avis['chauffeur_email']); ?>)</span>
                            </h6>
                            <p class="card-text mt-3 bg-light p-3 rounded italic">
                                "<?php echo htmlspecialchars($avis['commentaire']); ?>"
                            </p>
                        </div>
                        <div class="card-footer bg-white d-flex gap-2">
                            <a href="action_avis.php?id=<?php echo $avis['avis_id']; ?>&action=valider" 
                               class="btn btn-success flex-grow-1 fw-bold">Valider l'avis</a>
                            
                            <a href="action_avis.php?id=<?php echo $avis['avis_id']; ?>&action=refuser" 
                               class="btn btn-outline-danger" 
                               onclick="return confirm('Refuser et supprimer cet avis définitivement ?')">Refuser</a>
                        </div>
                    </div>
                </div>
                
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded shadow-sm">
                    <p class="text-muted mb-0">Tous les avis ont été traités.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="container mt-5 mb-5">
    <div class="card border-danger shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i> Covoiturages contestable (Notes 1 & 2 ★)</h5>
        </div>
        <div class="card-body">
            <?php if (count($incidents) > 0): ?>
                <div class="row">
                    <?php foreach ($incidents as $incident): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-start border-danger border-4 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="fw-bold text-danger">Trajet #<?php echo $incident['trajet_id']; ?></h6>
                                        <span class="badge bg-danger"><?php echo $incident['note']; ?> / 5 ★</span>
                                    </div>
                                    
                                    <div class="mt-2 small">
                                        <p class="mb-1"><strong>Départ :</strong> <?php echo htmlspecialchars($incident['ville_depart']); ?> le <?php echo date('d/m/Y', strtotime($incident['date_depart'])); ?> à <?php echo $incident['heure_depart']; ?></p>
                                        <p class="mb-1"><strong>Destination :</strong> <?php echo htmlspecialchars($incident['ville_arrivee']); ?></p>
                                    </div>

                                    <hr>
                                    
                                    <p class="mb-0 mt-2 small">
                                        <strong>Avis de <?php echo htmlspecialchars($incident['passager_nom']); ?> :</strong><br>
                                        <span class="fst-italic text-muted">"<?php echo htmlspecialchars($incident['commentaire']); ?>"</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success fs-1"></i>
                    <p class="text-muted mt-2">Aucun covoiturage contestable à signaler.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<footer class="bg-success text-white text-center py-3 mt-auto">
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>