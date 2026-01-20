<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'utilisateur') {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['utilisateur_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_auto'])) {
    $modele = trim($_POST['modele']);
    $immatriculation = trim($_POST['immatriculation']);
    $couleur = trim($_POST['couleur']);
    $date_immat = $_POST['date_immat'];
    $places = (int)$_POST['places'];
    $pref_fumeur = isset($_POST['pref_fumeur']) ? 1 : 0;
    $pref_animal = isset($_POST['pref_animal']) ? 1 : 0;
    $categorie = trim($_POST['categorie']);
    $est_electrique = isset($_POST['est_electrique']) ? 1 : 0;

    if (!empty($modele) && !empty($immatriculation)) {
        try {
            $sql_ins = "INSERT INTO voiture (modele, couleur, immatriculation, date_immatriculation, 
                        places_disponibles, pref_fumeur, pref_animal, categorie, est_electrique, utilisateur_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_ins = $pdo->prepare($sql_ins);
            $stmt_ins->execute([
                $modele, $couleur, $immatriculation, $date_immat, 
                $places, $pref_fumeur, $pref_animal, $categorie, $est_electrique, $user_id
            ]);
            
            header("Location: profil.php?succes=1");
            exit;
        } catch (PDOException $e) {
            $error_db = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    } else {
        $error_form = "Veuillez remplir les champs obligatoires.";
    }
}

$sql_user = "SELECT u.*, v.*,
            (SELECT AVG(note) FROM avis WHERE utilisateur_id = u.utilisateur_id AND est_valide = 1) as note_moyenne,
            (SELECT COUNT(*) FROM avis WHERE utilisateur_id = u.utilisateur_id AND est_valide = 1) as nb_avis
            FROM utilisateur u
            LEFT JOIN voiture v ON u.utilisateur_id = v.utilisateur_id
            WHERE u.utilisateur_id = ? LIMIT 1";

$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

if (!$user) {
    die("Erreur : Impossible de charger votre profil.");
}

$stmt_voitures = $pdo->prepare("SELECT * FROM voiture WHERE utilisateur_id = ?");
$stmt_voitures->execute([$user_id]);
$mes_voitures = $stmt_voitures->fetchAll();
$a_une_voiture = count($mes_voitures) > 0;

$stmt_mes_trajets = $pdo->prepare("SELECT trajet_id, ville_depart, ville_arrivee, date_depart, heure_depart, nb_place, prix, statut 
                                   FROM trajet 
                                   WHERE chauffeur_id = ? 
                                   ORDER BY date_depart DESC");
$stmt_mes_trajets->execute([$user_id]);
$mes_trajets_proposes = $stmt_mes_trajets->fetchAll();

$stmt_mes_reservations = $pdo->prepare("
    SELECT t.trajet_id, t.ville_depart, t.ville_arrivee, t.date_depart, t.heure_depart, t.statut, u.prenom as chauffeur_nom 
    FROM reservation r 
    JOIN trajet t ON r.trajet_id = t.trajet_id 
    JOIN utilisateur u ON t.chauffeur_id = u.utilisateur_id 
    WHERE r.utilisateur_id = ? 
    ORDER BY t.date_depart DESC
");
$stmt_mes_reservations->execute([$user_id]);
$mes_participations = $stmt_mes_reservations->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EcoRide - Mon Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">EcoRide</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="recherche.php">Accès aux Covoiturages</a></li>
                <li class="nav-item"><a class="nav-link active fw-bold" href="profil.php">Mon Profil</a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="deconnexion.php">Déconnexion</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="main-content container my-5">
    
    <?php if (isset($error_form) || isset($error_db)): ?>
        <div class="alert alert-danger shadow-sm"><?php echo $error_form ?? $error_db; ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['succes'])): ?>
        <div class="alert alert-success shadow-sm">Opération réussie !</div>
    <?php endif; ?>

    <div class="row g-4">
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4 mb-4">
                <div class="profile-pic-wrapper text-center">
                    <form id="form-photo" action="maj_photo.php" method="POST" enctype="multipart/form-data">
                        <label for="upload-photo" class="position-relative d-inline-block" style="cursor: pointer;">
                            <?php 
                            if ($user['sexe'] == 'F') {
                                $default_image = 'ProfilF.png';
                            } elseif ($user['sexe'] == 'H') {
                                $default_image = 'ProfilM.png';
                            } else {
                                $default_image = 'Voiture Ecoride.png';
                            }

                            if (!empty($user['photo_profil']) && file_exists("Image/" . $user['photo_profil'])) {
                                $image_path = "Image/" . $user['photo_profil'];
                            } else {
                                $image_path = "Image/" . $default_image;
                            }
                            ?>
                            <img src="<?php echo $image_path; ?>" 
                                class="rounded-circle mb-3 border p-1 mx-auto profile-img" 
                                width="100" height="100" 
                                style="object-fit: cover;"
                                alt="Photo de profil">
                            <div class="overlay rounded-circle d-flex align-items-center justify-content-center">
                                <span class="text-white small fw-bold">Changer ?</span>
                            </div>
                            <input type="file" name="nouvelle_photo" id="upload-photo" class="d-none" accept="image/*" onchange="document.getElementById('form-photo').submit();">
                        </label>
                    </form>
                </div>
                <h3 class="fw-bold mb-0"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h3>
                <div class="badge bg-success my-3 p-2"><?php echo $user['credit'] ?? 0; ?> Crédits</div>
            </div>

            <div class="card border-0 shadow-sm p-4">
            <h3 class="fw-bold text-success mb-4">Proposer un nouveau trajet</h3>

        <?php if ($a_une_voiture): ?>
        <form action="creer_trajet.php" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ville de départ</label>
                    <input type="text" name="ville_depart" class="form-control" placeholder="Ex: Paris" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ville d'arrivée</label>
                    <input type="text" name="ville_arrivee" class="form-control" placeholder="Ex: Lyon" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Date du voyage</label>
                    <input type="date" name="date_depart" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Heure de départ</label>
                    <input type="time" name="heure_depart" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Heure d'arrivée</label>
                    <input type="time" name="heure_arrivee" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Prix par passager</label>
                    <input type="number" name="prix" class="form-control" step="0.50" placeholder="Ex: 25" required>
                    <div class="form-text text-danger">2 crédits seront prélevés par la plateforme sur ce prix.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nombre de places</label>
                    <input type="number" name="nb_place" class="form-control" min="1" max="8" required>

                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Choisir votre véhicule</label>
                    <select name="voiture_id" class="form-select" required>
                        <option value="">-- Sélectionnez un véhicule --</option>
                        <?php foreach ($mes_voitures as $v): ?>
                            <option value="<?php echo $v['voiture_id']; ?>">
                                <?php echo htmlspecialchars($v['modele'] . " (" . $v['immatriculation'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">Publier mon trajet</button>
                </div>
            </div>
        </form>
                <div class="mt-2 text-end">
                    <button class="btn btn-sm btn-success" data-bs-toggle="collapse" data-bs-target="#collapseVoiture">Ajoutez un nouveau véhicule</button>
                </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center small">
                        Enregistrez un véhicule pour publier un trajet et devenir chauffeur.
                        <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="collapse" data-bs-target="#collapseVoiture">Ajouter ma voiture</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h4 class="fw-bold text-success mb-3 border-bottom pb-2">Mes Informations</h4>
                <div class="row">
                    <div class="col-6 mb-2"><span class="text-muted small">Email :</span><br><strong><?php echo htmlspecialchars($user['email']); ?></strong></div>
                    <div class="col-6 mb-2"><span class="text-muted small">Téléphone :</span><br><strong><?php echo htmlspecialchars($user['telephone'] ?? 'Non renseigné'); ?></strong></div>
                </div>
            </div>
        <?php if ($a_une_voiture): ?>
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h4 class="fw-bold text-success mb-3 border-bottom pb-2">Mes trajets mis en ligne</h4>
                <?php if (count($mes_trajets_proposes) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Itinéraire</th>
                                    <th>Places</th>
                                    <th>Prix</th>
                                    <th>Annulation</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mes_trajets_proposes as $tr): ?>
                                    <tr>
                                        <td class="small"><?php echo date('d/m/Y', strtotime($tr['date_depart'])); ?></td>
                                        <td><strong><?php echo htmlspecialchars($tr['ville_depart']); ?></strong> → <strong><?php echo htmlspecialchars($tr['ville_arrivee']); ?></strong></td>
                                        <td><span class="badge bg-info"><?php echo $tr['nb_place']; ?> restantes</span></td>
                                        <td class="fw-bold"><?php echo number_format($tr['prix'], 2); ?> €</td>
                                        <td>
                                            <?php if ($tr['statut'] === 'attente'): ?>
                                                <a href="supprimer_trajet.php?id=<?php echo $tr['trajet_id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Voulez-vous vraiment annuler votre course ?')">
                                                   Annuler
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-lock text-muted">
                                                    <i class="bi bi-patch-check"></i> Annulation impossible
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($tr['statut'] == 'attente'): ?>
                                                <a href="modifier_statut_trajet.php?id=<?php echo $tr['trajet_id']; ?>&action=demarrer" 
                                                   class="btn btn-success btn-sm w-100 fw-bold"
                                                   onclick="return confirm('Voulez-vous démarrer la course ?')">
                                                   Démarrer
                                                </a>

                                            <?php elseif ($tr['statut'] == 'en_cours'): ?>
                                                <a href="modifier_statut_trajet.php?id=<?php echo $tr['trajet_id']; ?>&action=clore" 
                                                   class="btn btn-primary btn-sm w-100 fw-bold"
                                                   onclick="return confirm('Êtes-vous arrivé à destination ?')">
                                                   Arrivée à destination
                                                </a>

                                            <?php elseif ($tr['statut'] == 'termine'): ?>
                                                <span class="badge bg-secondary w-100 p-2">Trajet clos</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted small">Vous n'avez pas encore publié de trajet.</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h4 class="fw-bold text-success mb-3 border-bottom pb-2">Mes réservations (Passager)</h4>
                <?php if (count($mes_participations) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Itinéraire</th>
                                    <th>Chauffeur</th>
                                    <th>Prix</th>
                                    <th>Statut</th>
                                    <th>Annulation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mes_participations as $res): ?>
                                    <tr>
                                        <td class="small"><?php echo date('d/m/Y', strtotime($res['date_depart'])); ?></td>
                                        <td><strong><?php echo htmlspecialchars($res['ville_depart']); ?></strong> → <strong><?php echo htmlspecialchars($res['ville_arrivee']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($res['chauffeur_nom']); ?></td>
                                        <td class="fw-bold text-success">Payé</td>
                                        <td class="fw-bold">
                                            <?php if ($res['statut'] == 'attente'): ?>
                                                <p class="mb-1">En attente</p>

                                            <?php elseif ($res['statut'] == 'en_cours'): ?>
                                                <p class="mb-1">Voyage en cours</p>

                                            <?php elseif ($res['statut'] == 'termine'): ?>
                                                <p class="mb-1">Trajet clos</p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($res['statut'] === 'attente'): ?>
                                                <a href="supprimer_trajet.php?id=<?php echo $res['trajet_id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Voulez-vous vraiment annuler votre réservation ?')">
                                                   Annuler
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-lock text-muted">
                                                    <i class="bi bi-patch-check"></i> Annulation impossible
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted small">Vous n'avez pas encore réservé de voyage.</p>
                <?php endif; ?>
            </div>
            
            <?php if ($user['modele']): ?>
                <div class="card border-0 shadow-sm p-4 bg-white border-start border-success border-4 mb-4">
                    <h4 class="fw-bold text-success mb-3">Véhicule Principal</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Modèle :</strong> <?php echo htmlspecialchars($user['modele']); ?></p>
                            <p class="mb-1"><strong>Immatriculation :</strong> <?php echo htmlspecialchars($user['immatriculation']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <span class="badge <?php echo $user['pref_fumeur'] ? 'bg-success' : 'bg-secondary'; ?>">Fumeur : <?php echo $user['pref_fumeur'] ? 'OK' : 'NON'; ?></span>
                            <span class="badge <?php echo $user['pref_animal'] ? 'bg-success' : 'bg-secondary'; ?>">Animaux : <?php echo $user['pref_animal'] ? 'OK' : 'NON'; ?></span>
                            <?php if (!empty($v['categorie'])): ?>
                                    <p class="mt-3 mb-0 small text-muted italic">
                                        <i class="bi bi-chat-dots"></i> "<?php echo htmlspecialchars($v['categorie']); ?>"
                                    </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="collapse" id="collapseVoiture">
                <div class="card card-body border-0 shadow-sm mb-4">
                    <h5 class="fw-bold text-success mb-3">Enregistrer un nouveau véhicule</h5>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Modèle</label>
                                <input type="text" name="modele" class="form-control" placeholder="Tesla Model 3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Immatriculation</label>
                                <input type="text" name="immatriculation" class="form-control" placeholder="AB-123-CD" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Date de 1ère immat.</label>
                                <input type="date" name="date_immat" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Places</label>
                                <input type="number" name="places" class="form-control" min="1" max="8" value="3" required>
                            </div>
        
                            <div class="col-12 border-top pt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="pref_fumeur" id="isFumeur">
                                    <label class="form-check-label" for="isFumeur">Fumeur OK</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="pref_animal" id="isAnimal">
                                    <label class="form-check-label" for="isAnimal">Animaux OK</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="est_electrique" id="isElec">
                                    <label class="form-check-label text-info fw-bold" for="isElec">Électrique</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Autres préférences</label>
                                <textarea name="categorie" class="form-control" rows="3" placeholder="Ex: J'aime écouter de la musique rock, j'ai souvent des bagages encombrants..."></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" name="ajouter_auto" class="btn btn-success w-100 fw-bold">Enregistrer le véhicule</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>

<footer class="bg-success text-white text-center py-3 mt-auto">
    <p class="mb-0">contact@ecoride.fr</p>
    <a href="mentions-legales.php" class="text-white">Mentions légales</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>