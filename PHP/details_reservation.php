<?php
session_start();
require_once '../db_connect.php';

$user_id = $_SESSION['utilisateur_id'] ?? null;
$id_trajet = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_trajet <= 0) {
    header('Location: recherche.php');
    exit;
}

$sql = "SELECT t.*, u.utilisateur_id as chauffeur_id, u.prenom, u.nom, u.sexe, u.photo_profil, u.telephone, 
               v.modele, v.couleur, v.pref_animal, v.pref_fumeur, v.immatriculation, v.est_electrique, v.categorie,
        (SELECT AVG(note) FROM avis WHERE utilisateur_id = u.utilisateur_id AND est_valide = 1) as note_moyenne,
        (SELECT COUNT(*) FROM avis WHERE utilisateur_id = u.utilisateur_id AND est_valide = 1) as nb_avis
        FROM trajet t
        JOIN utilisateur u ON t.chauffeur_id = u.utilisateur_id
        LEFT JOIN voiture v ON u.utilisateur_id = v.utilisateur_id
        WHERE t.trajet_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_trajet]);
$t = $stmt->fetch();

if (!$t) {
    header('Location: recherche.php');
    exit;
}

$credits_passager = 0;
if ($user_id) {
    $stmt_user = $pdo->prepare("SELECT credit FROM utilisateur WHERE utilisateur_id = ?");
    $stmt_user->execute([$user_id]);
    $user_info = $stmt_user->fetch();
    $credits_passager = $user_info['credit'] ?? 0;
}

if (isset($_POST['submit_message'])) {
    if (!$user_id) {
        header('Location: connexion.php');
        exit;
    }

    $trajet_id_post = intval($_POST['trajet_id']);
    $chauffeur_id_post = intval($_POST['chauffeur_id']);
    $contenu = trim($_POST['message']);

    if (!empty($contenu)) {
        try {
            $stmt_ins = $pdo->prepare("
                INSERT INTO messagerie (trajet_id, expediteur_id, destinataire_id, contenu, date_envoi) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt_ins->execute([$trajet_id_post, $user_id, $chauffeur_id_post, $contenu]);
            header("Location: messagerie.php?msg=envoye");
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error_msg = "Impossible d'envoyer le message.";
        }
    }
}

// Verification si l'utilisateur a deja donner son avi. De base la variable est fausse
$deja_donne = false;
if ($user_id) {
    $stmt_verif_avis = $pdo->prepare("SELECT COUNT(*) FROM avis 
                                      WHERE passager_id = ? 
                                      AND utilisateur_id = ? 
                                      AND trajet_id = ?");

    $stmt_verif_avis->execute([$user_id, $t['chauffeur_id'], $t['trajet_id']]);
    $deja_donne = $stmt_verif_avis->fetchColumn() > 0;
}

$stmt_last_avis = $pdo->prepare("SELECT a.*, u.prenom, u.photo_profil
                                 FROM avis a 
                                 JOIN utilisateur u ON a.passager_id = u.utilisateur_id 
                                 WHERE a.utilisateur_id = ? 
                                 AND a.est_valide = 1 
                                 ORDER BY a.avis_id DESC 
                                 LIMIT 1");

$stmt_last_avis->execute([$t['chauffeur_id']]);
$dernier_avis = $stmt_last_avis->fetch();
?>
<?php include('../components/header.php') ?>

<body class="d-flex flex-column min-vh-100 bg-light">

<?php include('../components/nav.php') ?>

<div class="container my-5 flex-grow-1">
    <div class="mb-4">
        <a href="javascript:history.back()" class="btn btn-outline-success btn-sm">
            ← Retour au profil
        </a>
    </div>

                <?php include('../components/details_components.php') ?>
            </div>
        </div>
        <div class="container mt-4">
            <div class="row g-4 justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100"> <div class="card-header bg-success text-white text-center py-3">
                        <h4 class="m-0 fw-bold">Laisser un avis</h4>
                    </div>
                        <div class="card-body p-4">
                            <form action="traitement_avis.php" method="POST">
                                <input type="hidden" name="trajet_id" value="<?php echo $_GET['id']; ?>">
                                <div class="mb-4 text-center">
                                    <label class="form-label d-block fw-bold">Note globale</label>
                                    <div class="btn-group" role="group">
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
                                    <div class="form-text">Votre avis sera soumis à la modération avant publication.</div>
                                </div>
                                <div class="d-grid">
                                    <?php if (!isset($_SESSION['utilisateur_id'])): ?>
                                    <a href="connexion.php" class="btn btn-warning btn-lg fw-bold shadow-sm">
                                        Connectez-vous pour laisser un avis
                                    </a>
                                <?php elseif ($deja_donne): ?>
                                    <div class="alert alert-info fw-bold">
                                        Vous avez déjà mis un avis pour ce trajet.
                                    </div>
                                <?php else: ?>
                                    <button type="submit" name="submit_avis" class="btn btn-success btn-lg fw-bold">
                                        Publier mon avis
                                    </button>
                                <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 overflow-hidden h-100">
                        <div class="card-header bg-success text-white text-center py-3">
                            <h4 class="m-0 fw-bold">Contacter le chauffeur</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="" method="POST">
                                <input type="hidden" name="trajet_id" value="<?php echo $_GET['id']; ?>">
                                <input type="hidden" name="chauffeur_id" value="<?php echo $t['chauffeur_id']; ?>">
                                <div class="mb-3">
                                    <label for="message" class="form-label fw-bold">Votre message</label>
                                    <textarea class="form-control" id="message" name="message" rows="8" 
                                              placeholder="Besoin de renseignement auprès du chauffeur ?" required></textarea>
                                    <div class="form-text">Votre message sera envoyé au chauffeur.</div>
                                </div>
                                <div class="d-grid mt-auto"> 
                                    <?php if (isset($_SESSION['utilisateur_id'])): ?>
                                        <button type="submit" name="submit_message" class="btn btn-success btn-lg fw-bold">
                                            Envoyer mon message
                                        </button>
                                    <?php else : ?>
                                        <a href="connexion.php" class="btn btn-warning btn-lg fw-bold shadow-sm">Connectez-vous pour laisser un message</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>

<?php include("../components/footer.html"); ?>

</body>