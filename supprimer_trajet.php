<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['id'])) {
    header('Location: profil.php');
    exit;
}

$id_cible = intval($_GET['id']);
$user_id = $_SESSION['utilisateur_id'];

try {
    $pdo->beginTransaction();

    $stmt_chef = $pdo->prepare("SELECT * FROM trajet WHERE trajet_id = ? AND chauffeur_id = ?");
    $stmt_chef->execute([$id_cible, $user_id]);
    $trajet_chauffeur = $stmt_chef->fetch();

    if ($trajet_chauffeur) {
        $stmt_p = $pdo->prepare("
            SELECT u.utilisateur_id, u.email, t.ville_depart, t.ville_arrivee, t.date_depart 
            FROM reservation r
            JOIN utilisateur u ON r.utilisateur_id = u.utilisateur_id
            JOIN trajet t ON r.trajet_id = t.trajet_id
            WHERE t.trajet_id = ?
        ");
        $stmt_p->execute([$id_cible]);
        $passagers = $stmt_p->fetchAll();

        $stmt_rembourse = $pdo->prepare("UPDATE utilisateur SET credit = credit + 2 WHERE utilisateur_id = ?");

        foreach ($passagers as $p) {
            $stmt_rembourse->execute([$p['utilisateur_id']]);

            $to = $p['email'];
            $subject = "Annulation de votre trajet EcoRide";
            $message = "Bonjour, \n\nLe trajet " . htmlspecialchars($p['ville_depart']) . " - " . htmlspecialchars($p['ville_arrivee']) . " a été annulé par le chauffeur. Vos 2 crédits ont été remboursés.";
            @mail($to, $subject, $message, "From: ne-pas-repondre@ecoride.fr");
        }

        $del = $pdo->prepare("DELETE FROM trajet WHERE trajet_id = ?");
        $del->execute([$id_cible]);
        
        $msg_succes = "annule_rembourse";

    } else {
        $stmt_res = $pdo->prepare("
            SELECT t.statut FROM reservation r 
            JOIN trajet t ON r.trajet_id = t.trajet_id 
            WHERE r.trajet_id = ? AND r.utilisateur_id = ?
        ");
        $stmt_res->execute([$id_cible, $user_id]);
        $reservation = $stmt_res->fetch();

        if ($reservation && $reservation['statut'] === 'attente') {
            $del_res = $pdo->prepare("DELETE FROM reservation WHERE trajet_id = ? AND utilisateur_id = ?");
            $del_res->execute([$id_cible, $user_id]);

            $up_credit = $pdo->prepare("UPDATE utilisateur SET credit = credit + 2 WHERE utilisateur_id = ?");
            $up_credit->execute([$user_id]);
            
            $msg_succes = "annulation_ok";
        } else {
            throw new Exception("annulation_interdite");
        }
    }

    $pdo->commit();
    header("Location: profil.php?succes=$msg_succes");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $error_code = ($e->getMessage() === "annulation_interdite") ? "annulation_interdite" : "erreur_critique";
    header("Location: profil.php?error=$error_code");
    exit;
}