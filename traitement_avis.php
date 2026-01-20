<?php
session_start();
require_once 'db_connect.php';

if (isset($_POST['submit_avis']) && isset($_SESSION['utilisateur_id'])) {
    $trajet_id = intval($_POST['trajet_id']);
    $passager_id = $_SESSION['utilisateur_id'];
    $note = intval($_POST['note']);
    $commentaire = trim(htmlspecialchars($_POST['commentaire']));

    $stmt = $pdo->prepare("SELECT chauffeur_id FROM trajet WHERE trajet_id = ?");
    $stmt->execute([$trajet_id]);
    $trajet = $stmt->fetch();

    if ($trajet) {
        $sql = "INSERT INTO avis (passager_id, utilisateur_id, trajet_id, commentaire, note, est_valide, etoiles) 
                VALUES (?, ?, ?, ?, ?, 0, ?)";
        
        $stmt_ins = $pdo->prepare($sql);
        $stmt_ins->execute([
            $passager_id,
            $trajet['chauffeur_id'],
            $trajet_id,
            $commentaire, 
            $note, 
            $note
        ]);

        header("Location: profil.php?succes=avis_envoye");
        exit;
    }
}
header("Location: profil.php");