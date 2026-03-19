<?php
session_start();
require_once '../db_connect.php';

$id_trajet = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_trajet <= 0) {
    header('Location: recherche.php');
    exit;
}
$sql = "SELECT t.*, u.utilisateur_id as chauffeur_id, u.prenom, u.nom, u.sexe, u.photo_profil, u.telephone, v.modele, v.couleur, v.pref_animal, v.pref_fumeur, v.immatriculation, v.est_electrique, v.categorie,
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
<?php include('../components/header.php') ?>

<body class="d-flex flex-column min-vh-100 bg-light">

<?php include('../components/nav.php') ?>

<div class="container my-5 flex-grow-1">
    <div class="mb-4">
        <a href="javascript:history.back()" class="btn btn-outline-success btn-sm">
            ← Retour aux résultats
        </a>
    </div>

    <?php include('../components/details_components.php') ?>

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
    </div>
</div>

<?php include("../components/footer.html"); ?>

</body>