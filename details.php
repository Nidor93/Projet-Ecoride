<?php
session_start();
require_once 'db_connect.php';

$id_trajet = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_trajet <= 0) {
    header('Location: recherche.php');
    exit;
}
$sql = "SELECT t.*, u.utilisateur_id as chauffeur_id, u.prenom, u.nom, u.sexe, u.photo_profil, v.modele, v.couleur, v.pref_animal, v.pref_fumeur, v.immatriculation, v.est_electrique, v.categorie,
        (SELECT AVG(note) FROM avis WHERE utilisateur_id = u.utilisateur_id AND est_valide = 1) as note_moyenne,
        (SELECT COUNT(*) FROM avis WHERE utilisateur_id = u.utilisateur_id AND est_valide = 1) as nb_avis
        FROM trajet t
        JOIN utilisateur u ON t.chauffeur_id = u.utilisateur_id
        LEFT JOIN voiture v ON u.utilisateur_id = v.utilisateur_id
        WHERE t.trajet_id = ?";
        
$credits_passager = 0;
if (isset($_SESSION['utilisateur_id'])) {
    $stmt_user = $pdo->prepare("SELECT credit FROM utilisateur WHERE utilisateur_id = ?");
    $stmt_user->execute([$_SESSION['utilisateur_id']]);
    $user_connected = $stmt_user->fetch();
    $credits_passager = $user_connected['credit'] ?? 0;
}

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_trajet]);
$t = $stmt->fetch();

if (!$t) {
    die("Erreur : Ce trajet n'existe pas.");
}

$stmt_last_avis = $pdo->prepare("
    SELECT a.*, u.prenom, u.photo_profil
    FROM avis a 
    JOIN utilisateur u ON a.passager_id = u.utilisateur_id 
    WHERE a.utilisateur_id = ? 
    AND a.est_valide = 1 
    ORDER BY a.avis_id DESC 
    LIMIT 1
");

$stmt_last_avis->execute([$t['chauffeur_id']]);
$dernier_avis = $stmt_last_avis->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EcoRide - Détails du voyage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">EcoRide</a>
        <div class="collapse navbar-collapse">
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
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5 flex-grow-1">
    <div class="mb-4">
        <a href="javascript:history.back()" class="btn btn-outline-success btn-sm">
            ← Retour aux résultats
        </a>
    </div>

    <h2 class="fw-bold text-success mb-4 text-center">Détails du voyage</h2>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 h-100">
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"></div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo substr($t['heure_depart'], 0, 5); ?> - <?php echo htmlspecialchars($t['ville_depart']); ?></h5>
                        </div>
                    </div>
                    <div class="ms-3 mb-3" style="border-left: 2px dashed #198754; height: 40px; width: 2px;"></div>
                    <div class="d-flex align-items-center">
                        <div class="bg-dark text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"></div>
                        <div>
                            <h5 class="fw-bold mb-0">
                                <?php if(isset($t['heure_arrivee'])) echo substr($t['heure_arrivee'], 0, 5) . ' - '; ?>
                                <?php echo htmlspecialchars($t['ville_arrivee']); ?>
                            </h5>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mt-4">
                    <h6 class="text-muted text-uppercase small fw-bold mb-3">Infos véhicule & Règles à bord</h6>
                    <div class="p-3 bg-light rounded shadow-sm">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Modèle :</strong> <?php echo htmlspecialchars($t['modele'] ?? 'Non renseigné'); ?></p>
                                <p class="mb-1"><strong>Couleur :</strong> <?php echo htmlspecialchars($t['couleur'] ?? '-'); ?></p>
                                <p class="mb-1"><strong>Immatriculation :</strong> <?php echo htmlspecialchars($t['immatriculation'] ?? 'Non renseigné'); ?></p>
                            </div>
                            <div class="col-md-6 border-start border-white">
                                <p class="mb-2"><strong>Préférences :</strong></p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge <?php echo $t['pref_fumeur'] ? 'bg-success' : 'bg-danger'; ?> p-2">
                                        <?php echo $t['pref_fumeur'] ? ' Fumeur accepté' : ' Non-fumeur'; ?>
                                    </span>
                                    <span class="badge <?php echo $t['pref_animal'] ? 'bg-success' : 'bg-danger'; ?> p-2">
                                        <?php echo $t['pref_animal'] ? ' Animaux OK' : ' Pas d\'animaux'; ?>
                                    </span>
                                    <?php if (!empty($t['est_electrique'])): ?>
                                        <span class="badge bg-success-subtle text-success border border-success"> Électrique</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Thermique</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($t['categorie'])): ?>
                                    <p class="mt-3 mb-0 small text-muted italic">
                                        <i class="bi bi-chat-dots"></i> "<?php echo htmlspecialchars($t['categorie']); ?>"
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                    <h5 class="fw-bold mb-3">Avis de nos passagers</h5>
                    <?php if ($dernier_avis): ?>
                        <div class="card p-3 border-0 bg-light shadow-sm">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fw-bold me-2"><?php echo htmlspecialchars($dernier_avis['prenom']); ?></span>
                                <div class="text-warning small">
                                    <?php echo str_repeat('★', $dernier_avis['note']); ?>
                                </div>
                            </div>
                            <p class="mb-0 italic">"<?php echo htmlspecialchars($dernier_avis['commentaire']); ?>"</p>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small">Aucun avis pour le moment.</p>
                    <?php endif; ?>
        </div>
    </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm border-top border-success border-4 p-4 text-center h-100">
                <div class="mb-4">
                    <?php
                            if ($t['sexe'] == 'F') {
                                $default_img = 'ProfilF.png';
                            } elseif ($t['sexe'] == 'H') {
                                $default_img = 'ProfilM.png';
                            } else {
                                $default_img = 'Voiture Ecoride.png';
                            }

                            if (!empty($t['photo_profil']) && file_exists("Image/" . $t['photo_profil'])) {
                                $img_chauffeur = "Image/" . $t['photo_profil'];
                            } else {
                                $img_chauffeur = "Image/" . $default_img;
                            }
                            ?>
                            <img src="<?php echo $img_chauffeur; ?>" class="rounded-circle" width="100" height="100" style="object-fit: cover;">
                    <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($t['prenom'] . ' ' . $t['nom']); ?></h4>
                    <p class="text-warning mb-0">
                        <?php 
                        $note = $t['note_moyenne'] ? round($t['note_moyenne']) : 0;
                        for($i=1; $i<=5; $i++) echo ($i <= $note) ? '★' : '☆';
                        ?>
                        <span class="text-muted small ms-1">(<?php echo $t['nb_avis']; ?> avis)</span>
                    </p>
                </div>

                <div class="bg-light p-3 rounded mb-4">
                    <span class="text-muted d-block small mb-1">Prix par passager</span>
                    <span class="h2 fw-bold text-success mb-0"><?php echo number_format($t['prix'], 2); ?>€</span>
                </div>
                
                <p class="text-muted mb-4">
                    <i class="bi bi-people"></i> <?php echo $t['nb_place']; ?> places restantes
                </p>

                <div class="d-grid gap-2">
                    <?php if (!isset($_SESSION['utilisateur_id'])): ?>
                        <a href="connexion.php" class="btn btn-warning btn-lg fw-bold shadow-sm">Connectez-vous pour participer</a>
                    <?php elseif ($t['nb_place'] <= 0): ?>
                        <button class="btn btn-secondary btn-lg fw-bold" disabled>Trajet Complet</button>
                    <?php elseif ($credits_passager < 2): ?>
                        <button class="btn btn-danger btn-lg fw-bold" disabled>Crédits insuffisants (2)</button>
                    <?php else: ?>
                        <button onclick="confirmerParticipation()" class="btn btn-success btn-lg fw-bold shadow-sm">Participer au voyage</button>
                        <small class="text-muted mt-1 small">Prélèvement de 2 crédits inclus</small>
                    <?php endif; ?>
                </div>

                <script>
                    function confirmerParticipation() {
                        if (confirm("Voulez-vous réserver votre place sur ce trajet ?")) {
                            if (confirm("Confirmez-vous l'utilisation de 2 crédits pour valider la participation ?")) {
                                window.location.href = "participer.php?id=<?php echo $t['trajet_id']; ?>";
                            }
                        }
                    }
                </script>
            </div>
        </div>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success text-white text-center py-3">
                            <h4 class="mb-0">Laisser un avis</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="traitement_avis.php" method="POST">
                                <input type="hidden" name="trajet_id" value="<?php echo $_GET['id']; ?>">

                                <div class="mb-4 text-center">
                                    <label class="form-label d-block fw-bold">Note globale</label>
                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <input type="radio" class="btn-check" name="note" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                                            <label class="btn btn-outline-warning" for="star<?php echo $i; ?>">
                                                <?php echo $i; ?> ★
                                            </label>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="commentaire" class="form-label fw-bold">Votre commentaire</label>
                                    <textarea class="form-control" id="commentaire" name="commentaire" rows="4" 
                                        placeholder="Racontez votre expérience..." required></textarea>
                                    <div class="form-text">Votre avis sera soumis à modération avant publication.</div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" name="submit_avis" class="btn btn-success btn-lg fw-bold">
                                        Publier mon avis
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>
<footer class="bg-success text-white text-center py-3 mt-auto shadow-lg">
    <p class="mb-1">contact@ecoride.fr</p>
    <a href="mentions-legales.php" class="text-white text-decoration-underline">Mentions légales</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>