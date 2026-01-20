<?php
session_start();
require_once 'db_connect.php';

$duree_heures = !empty($_GET['duree_max_h']) ? floatval($_GET['duree_max_h']) : 999;
$duree_max_minutes = $duree_heures * 60;

$depart = "%" . ($_GET['depart'] ?? '') . "%";
$arrivee = "%" . ($_GET['arrivee'] ?? '') . "%";
$prix_max = !empty($_GET['prix_max']) ? floatval($_GET['prix_max']) : 9999;
$eco_only = isset($_GET['eco_only']) ? 1 : 0;
$etoiles_min = !empty($_GET['etoiles_min']) ? floatval($_GET['etoiles_min']) : 0;

$sql = "SELECT t.*, v.est_electrique, u.prenom, u.sexe, u.photo_profil,
        (SELECT AVG(note) FROM avis WHERE utilisateur_id = t.chauffeur_id AND est_valide = 1) as etoiles
        FROM trajet t
        INNER JOIN utilisateur u ON t.chauffeur_id = u.utilisateur_id
        LEFT JOIN voiture v ON u.utilisateur_id = v.utilisateur_id
        WHERE t.ville_depart LIKE ? 
        AND t.ville_arrivee LIKE ? 
        AND t.prix <= ? 
        AND TIMESTAMPDIFF(MINUTE, t.heure_depart, t.heure_arrivee) <= ?";

if ($eco_only) {
    $sql .= " AND v.est_electrique = 1";
}

$sql .= " HAVING (etoiles >= ? OR etoiles IS NULL)";

$sql .= " ORDER BY t.heure_depart ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $depart,
    $arrivee,
    $prix_max,
    $duree_max_minutes,
    $etoiles_min
]);

$trajets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EcoRide - Rechercher un trajet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="style.css" rel="stylesheet">
    <style>
        .ride-card { transition: transform 0.2s; cursor: pointer; }
        .ride-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .img-avatar { width: 60px; height: 60px; object-fit: cover; border: 2px solid #198754; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">EcoRide</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link active fw-bold" href="recherche.php">Accès aux Covoiturages</a></li>
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

<div class="container my-5">
    <div class="row">
        <aside class="col-lg-3">
            <div class="card p-4 shadow-sm border-0 sticky-top" style="top: 20px;">
                <h5 class="fw-bold mb-4"><i class="bi bi-sliders"></i> Filtres</h5>
                <form method="GET" action="recherche.php" class="d-flex flex-column">
                    <input type="hidden" name="depart" value="<?php echo htmlspecialchars($_GET['depart'] ?? ''); ?>">
                    <input type="hidden" name="arrivee" value="<?php echo htmlspecialchars($_GET['arrivee'] ?? ''); ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Prix Max</label>
                        <div class="input-group">
                            <input type="number" name="prix_max" class="form-control" value="<?php echo htmlspecialchars($_GET['prix_max'] ?? ''); ?>">
                            <span class="input-group-text">€</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Durée Max (heures)</label>
                        <div class="input-group">
                            <input type="number" name="duree_max_h" step="0.5" min="0" class="form-control" 
                                   value="<?php echo htmlspecialchars($_GET['duree_max_h'] ?? ''); ?>" placeholder="Ex: 3">
                            <span class="input-group-text">h</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Note minimale</label>
                        <select name="etoiles_min" class="form-select">
                            <option value="0">Toutes</option>
                            <?php for($i=1; $i<=5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($_GET['etoiles_min'] ?? '') == $i ? 'selected' : ''; ?>><?php echo $i; ?>+ ★</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="eco_only" id="eco" value="1" <?php echo isset($_GET['eco_only']) ? 'checked' : ''; ?>>
                        <label class="form-check-label small" for="eco">Électrique uniquement</label>
                    </div>

                    <button type="submit" class="btn btn-success fw-bold w-100 mb-2">Appliquer</button>
                    <a href="recherche.php?depart=<?php echo urlencode($_GET['depart'] ?? ''); ?>&arrivee=<?php echo urlencode($_GET['arrivee'] ?? ''); ?>" class="btn btn-link btn-sm text-secondary text-decoration-none text-center">Réinitialiser</a>
                </form>
            </div>
        </aside>

        <main class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 fw-bold mb-0">Trajets disponibles</h2>
                <?php 
                $trajets_actifs = array_filter($trajets, function($t) {
                    return $t['statut'] !== 'termine';
                });
                ?>
                <span class="badge bg-dark px-3 py-2">
                    <?php echo count($trajets_actifs); ?> résultat(s)
                </span>
            </div>

            <?php if (empty($trajets)): ?>
                <div class="alert alert-white border shadow-sm p-5 text-center">
                    <i class="bi bi-search h1 text-muted"></i>
                    <p class="mt-3 mb-0">Aucun trajet ne correspond à vos critères.</p>
                </div>
            <?php else: ?>
                <?php foreach ($trajets as $t): 
                    if (isset($t['statut']) && $t['statut'] === 'termine') {
                    continue; 
                    }
                    $start = new DateTime($t['heure_depart']);
                    $end = new DateTime($t['heure_arrivee']);
                    $duree = $start->diff($end)->format('%h h %i min');

                    $default = ($t['sexe'] == 'F') ? 'ProfilF.png' : (($t['sexe'] == 'H') ? 'ProfilM.png' : 'Voiture Ecoride.png');
                    $img = (!empty($t['photo_profil']) && file_exists("Image/" . $t['photo_profil'])) ? "Image/" . $t['photo_profil'] : "Image/" . $default;
                ?>
                <div class="ride-card card p-3 shadow-sm mb-3 border-0">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center border-end">
                            <img src="<?php echo $img; ?>" class="rounded-circle img-avatar mb-2">
                            <div class="small fw-bold"><?php echo htmlspecialchars($t['prenom']); ?></div>
                            <div class="text-warning small">
                                <?php 
                                $n = round($t['etoiles'] ?? 0);
                                for($i=1; $i<=5; $i++) echo ($i <= $n) ? '★' : '☆';
                                ?>
                            </div>
                        </div>

                        <div class="col-md-5 px-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="h5 fw-bold mb-0"><?php echo substr($t['heure_depart'], 0, 5); ?></span>
                                <span class="small text-secondary mb-3"><?php echo $duree; ?></span>
                                <span class="h5 fw-bold mb-0"><?php echo substr($t['heure_arrivee'], 0, 5); ?></span>
                            </div>
                            <div class="d-flex justify-content-between small text-secondary mb-3">
                                <span><?php echo htmlspecialchars($t['ville_depart']); ?></span>
                                <span><?php echo htmlspecialchars($t['ville_arrivee']); ?></span>
                            </div>
                            <?php if ($t['est_electrique']): ?>
                                <span class="badge bg-success-subtle text-success border border-success">Éco-voyage</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border">Thermique</span>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3 text-center border-start">
                            <div class="text-muted small">Places restantes</div>
                            <div class="fw-bold h5"><?php echo $t['nb_place']; ?></div>
                            <div class="text-success h4 fw-bold"><?php echo $t['prix']; ?>€</div>
                        </div>

                        <div class="col-md-2 text-end">
                            <a href="details.php?id=<?php echo $t['trajet_id']; ?>" class="btn btn-success w-100 py-2 fw-bold">Détails</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<footer class="bg-success text-white text-center py-3 mt-auto">
    <p class="mb-0">contact@ecoride.fr</p>
    <a href="mentions-legales.php" class="text-white">Mentions légales</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>