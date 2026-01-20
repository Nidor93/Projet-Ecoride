<?php
session_start();
require_once 'db_connect.php';


if (isset($_GET['id']) && isset($_GET['action'])) {
    $avis_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'valider') {
        $stmt = $pdo->prepare("UPDATE avis SET est_valide = 1 WHERE avis_id = ?");
        $stmt->execute([$avis_id]);
        $msg = "valide";
    } 
    elseif ($action === 'refuser') {
        $stmt = $pdo->prepare("DELETE FROM avis WHERE avis_id = ?");
        $stmt->execute([$avis_id]);
        $msg = "refuse";
    }

    header("Location: profil_employe.php?msg=" . $msg);
    exit;
}