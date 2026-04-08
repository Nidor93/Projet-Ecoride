<?php
session_start();
require_once '../db_connect.php';
$error_form = null;
$error_db = null;

if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'utilisateur') {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['utilisateur_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['email']) && isset($_POST['telephone'])) {
        $email = htmlspecialchars(trim($_POST['email']));
        $numero = htmlspecialchars(trim($_POST['telephone']));

        if (!empty($email) && !empty($numero)) {
            try { 
                $sql = "UPDATE utilisateur SET email = ?, telephone = ? WHERE utilisateur_id = ?";
                $pdo->prepare($sql)->execute([$email, $numero, $user_id]);
                header("Location: profil.php?succes=1");
                exit;
            } catch (PDOException $e) {
                $error_db = "Erreur profil : " . $e->getMessage();
            }
        }
    }

    if (isset($_POST['modele']) && isset($_POST['immatriculation'])) {
        $modele = trim($_POST['modele']);
        $immatriculation = trim($_POST['immatriculation']);
        $couleur = trim($_POST['couleur'] ?? '');
        $date_immat = $_POST['date_immat'] ?? null;
        $places = (int)($_POST['places'] ?? 0);
        $pref_fumeur = isset($_POST['pref_fumeur']) ? 1 : 0;
        $pref_animal = isset($_POST['pref_animal']) ? 1 : 0;
        $categorie = trim($_POST['categorie'] ?? '');
        $est_electrique = isset($_POST['est_electrique']) ? 1 : 0;
        
        $voiture_id_changer = !empty($_POST['voiture_id']) ? (int)$_POST['voiture_id'] : null;

        if (!empty($modele) && !empty($immatriculation)) {
            try {
                if ($voiture_id_changer) {
                    $sql = "UPDATE voiture SET modele = ?, immatriculation = ?, couleur = ?, date_immatriculation = ?, places_disponibles = ?, pref_fumeur = ?, pref_animal = ?, categorie = ?, est_electrique = ? WHERE voiture_id = ? AND utilisateur_id = ?";
                    $params = [$modele, $immatriculation, $couleur, $date_immat, $places, $pref_fumeur, $pref_animal, $categorie, $est_electrique, $voiture_id_changer, $user_id];
                } else {
                    $sql = "INSERT INTO voiture (modele, immatriculation, couleur, date_immatriculation, places_disponibles, pref_fumeur, pref_animal, categorie, est_electrique, utilisateur_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $params = [$modele, $immatriculation, $couleur, $date_immat, $places, $pref_fumeur, $pref_animal, $categorie, $est_electrique, $user_id];
                }

                $pdo->prepare($sql)->execute($params);
                header("Location: profil.php?succes=1");
                exit;

            } catch (PDOException $e) {
                $error_db = "Erreur véhicule : " . $e->getMessage();
            }
        } else {
            $error_form = "Veuillez remplir les champs obligatoires.";
        }
    }
}

$sql_user = "SELECT u.*, 
            v.voiture_id AS vehicule_id, v.modele, v.immatriculation, v.couleur, v.date_immatriculation, 
            v.places_disponibles, v.pref_fumeur, v.pref_animal, v.categorie, v.est_electrique,
            (SELECT AVG(note) FROM avis WHERE utilisateur_id = u.utilisateur_id AND est_valide = 1) as note_moyenne,
            (SELECT COUNT(*) FROM avis WHERE utilisateur_id = u.utilisateur_id AND est_valide = 1) as nb_avis
            FROM utilisateur u
            LEFT JOIN voiture v ON u.utilisateur_id = v.utilisateur_id
            WHERE u.utilisateur_id = ? 
            ORDER BY v.voiture_id DESC LIMIT 1"; 

$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

if (!$user) {
    die("Erreur : Impossible de charger votre profil.");
}

$stmt_voitures = $pdo->prepare("SELECT * FROM voiture WHERE utilisateur_id = ?");
$stmt_voitures->execute([$user_id]);
$mes_voitures = $stmt_voitures->fetchAll();
$a_une_voiture = (count($mes_voitures) > 0);

$stmt_mes_trajets = $pdo->prepare("SELECT * FROM trajet WHERE chauffeur_id = ? ORDER BY date_depart DESC");
$stmt_mes_trajets->execute([$user_id]);
$mes_trajets_proposes = $stmt_mes_trajets->fetchAll();

$stmt_mes_reservations = $pdo->prepare("SELECT t.*, u.prenom as chauffeur_nom 
                                        FROM reservation r 
                                        JOIN trajet t ON r.trajet_id = t.trajet_id 
                                        JOIN utilisateur u ON t.chauffeur_id = u.utilisateur_id 
                                        WHERE r.utilisateur_id = ? 
                                        ORDER BY t.date_depart DESC");
$stmt_mes_reservations->execute([$user_id]);
$mes_participations = $stmt_mes_reservations->fetchAll();
?>

<?php include('../components/header.php') ?>

<body class="d-flex flex-column min-vh-100 bg-light">

<?php include('../components/nav.php') ?>

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
                    <?php include("../form/maj_photo_form.php"); ?>
                </div>
                <h3 class="fw-bold mb-0"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h3>
                <div class="badge bg-success my-3 p-2"><?php echo $user['credit'] ?? 0; ?> Crédits</div>
            </div>

            <div class="card border-0 shadow-sm p-4">
            <h3 class="fw-bold text-success mb-4">Proposer un nouveau trajet</h3>

        <?php if ($a_une_voiture): ?>
            <?php include("../form/creer_trajet_form.php"); ?>
        
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
            
            <div class="card border-0 shadow-sm p-4 border-bottom border-success border-4 mb-4">
                <h4 class="fw-bold text-success mb-3 border-bottom pb-2">Mes Informations</h4>
                <div class="row">
                    <div class="col-6 mb-2"><span class="text-muted small">Email :</span><br><strong><?php echo htmlspecialchars($user['email']); ?></strong></div>
                    <div class="col-6 mb-2"><span class="text-muted small">Téléphone : </span><br><strong>0<?php echo htmlspecialchars($user['telephone'] ?? 'Non renseigné'); ?></strong></div>
                    <div class="mt-2 text-end">
                        <button class="btn btn-sm btn-success" data-bs-toggle="collapse" data-bs-target="#collapseInformations">Changer mes informations ?</button>
                    </div>
                </div>
            </div>
            <div class="collapse" id="collapseInformations">
                <div class="card card-body border-0 shadow-sm border-bottom border-success border-4 mb-4">
                    <h5 class="fw-bold text-success mb-3">Enregistrer de nouvelles informations</h5>
                    <?php include("../form/maj_infos_form.html"); ?>
                    
                </div>
            </div>
        <?php if ($a_une_voiture): ?>
            <?php include("../components/profil_tab_mes_trajets.php"); ?>
            <?php endif; ?>

            <?php include("../components/profil_tab_mes_reservations.php"); ?>
            
            <?php if ($a_une_voiture): ?>
                <h4 class="fw-bold text-success mb-3">Mes Véhicules</h4>
    
                <?php foreach ($mes_voitures as $v): ?>
                    <div class="card border-0 shadow-sm p-4 bg-white border-bottom border-success border-4 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <p class="mb-1"><strong>Modèle :</strong> <?php echo htmlspecialchars($v['modele']); ?></p>
                                <p class="mb-1"><strong>Immatriculation :</strong> <?php echo htmlspecialchars($v['immatriculation']); ?></p>
                            </div>
                            <div class="col-md-4">
                                <span class="badge <?php echo $v['pref_fumeur'] ? 'bg-success' : 'bg-secondary'; ?>">Fumeur : <?php echo $v['pref_fumeur'] ? 'OK' : 'NON'; ?></span>
                                <span class="badge <?php echo $v['pref_animal'] ? 'bg-success' : 'bg-secondary'; ?>">Animaux : <?php echo $v['pref_animal'] ? 'OK' : 'NON'; ?></span>
                                <?php if ($v['est_electrique']): ?>
                                    <span class="badge bg-info text-dark">Électrique</span>
                                <?php endif; ?>
                                <?php if (!empty($v['categorie'])): ?>
                                    <p class="mt-3 mb-0 small text-muted italic">
                                        <i class="bi bi-chat-dots"></i> "<?php echo htmlspecialchars($v['categorie']); ?>"
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3 text-end">
                                <a href="supprimer_voiture.php?id=<?php echo $v['voiture_id']; ?>" 
                                   class="btn btn-sm btn-outline-danger mb-2 w-100"
                                   onclick="return confirm('Attention : La suppression de ce véhicule annulera les trajets associés. Confirmer ?')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </a>
                                <button class="btn btn-success btn-sm w-100 fw-bold" data-bs-toggle="collapse" data-bs-target="#collapseUpdate<?php echo $v['voiture_id']; ?>">
                                    Modifier
                                </button>
                            </div>
                        </div>

                        <div class="collapse mt-3" id="collapseUpdate<?php echo $v['voiture_id']; ?>">
                            <div class="card card-body border-0 bg-light">
                                <h5 class="fw-bold text-success mb-3">Mettre à jour <?php echo htmlspecialchars($v['modele']); ?></h5>
                                <?php $voiture_actuelle = $v; include("../form/maj_infos_voiture_form.php"); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="collapse" id="collapseVoiture">
                <div class="card card-body border-0 shadow-sm border-bottom border-success border-4 mb-4">
                    <h5 class="fw-bold text-success mb-3">Enregistrer un nouveau véhicule</h5>
                    <?php include("../form/nouveau_vehicule_form.html"); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../components/footer.html"); ?>

</body>