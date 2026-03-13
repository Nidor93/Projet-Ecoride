<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['id'])) {
    header('Location: profil.php');
    exit;
}

$id_cible = intval($_GET['id']);
$user_id = $_SESSION['utilisateur_id'];

try {
    $pdo->beginTransaction();

    $stmt_chef = $pdo->prepare("SELECT * FROM voiture WHERE voiture_id = ? AND utilisateur_id = ?");
    $stmt_chef->execute([$id_cible, $user_id]);
    $voiture_utilisateur = $stmt_chef->fetch();

    if ($voiture_utilisateur) {
        $stmt_p = $pdo->prepare("
            SELECT DISTINCT u.utilisateur_id, u.email, t.ville_depart, t.ville_arrivee
            FROM reservation r
            JOIN trajet t ON r.trajet_id = t.trajet_id
            JOIN utilisateur u ON r.utilisateur_id = u.utilisateur_id
            WHERE t.voiture_id = ?
        ");
        $stmt_p->execute([$id_cible]);
        $passagers = $stmt_p->fetchAll();

        $stmt_rembourse = $pdo->prepare("UPDATE utilisateur SET credit = credit + 2 WHERE utilisateur_id = ?");

        foreach ($passagers as $p) {
            $stmt_rembourse->execute([$p['utilisateur_id']]);

            $to = $p['email'];
            $subject = "Annulation de votre trajet EcoRide";
            $message = "Bonjour, \n\nLe trajet " . htmlspecialchars($p['ville_depart']) . " - " . htmlspecialchars($p['ville_arrivee']) . " a été annulé car le chauffeur a supprimé son véhicule. Vos crédits ont été remboursés.";
            @mail($to, $subject, $message, "From: ne-pas-repondre@ecoride.fr");
        }

        $del_res = $pdo->prepare("DELETE FROM reservation WHERE trajet_id IN (SELECT trajet_id FROM trajet WHERE voiture_id = ?)");
        $del_res->execute([$id_cible]);

        $del_trajets = $pdo->prepare("DELETE FROM trajet WHERE voiture_id = ?");
        $del_trajets->execute([$id_cible]);

        $del_voiture = $pdo->prepare("DELETE FROM voiture WHERE voiture_id = ?");
        $del_voiture->execute([$id_cible]);

        $pdo->commit();
        $msg = "succes=suppression_validee";

    } else {
        $msg = "error=erreur_proprietaire";
    }
    
    header("Location: profil.php?$msg");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header("Location: profil.php?error=erreur_critique");
    exit;
}