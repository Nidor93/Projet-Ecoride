<?php
session_start();
require_once '../db_connect.php';

$id_voiture = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['utilisateur_id'] ?? 0;

if ($id_voiture <= 0 || $user_id <= 0) {
    die("Erreur : Paramètres invalides.");
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT voiture_id FROM voiture WHERE voiture_id = ? AND utilisateur_id = ?");
    $stmt->execute([$id_voiture, $user_id]);
    if (!$stmt->fetch()) {
        die("Erreur : Ce véhicule ne vous appartient pas.");
    }

    // Suppression de chaques éléments lier au véhicule avant de supprimer le véhicule
    $sql_msg = "DELETE FROM messagerie WHERE trajet_id IN (SELECT trajet_id FROM trajet WHERE voiture_id = ?)";
    $pdo->prepare($sql_msg)->execute([$id_voiture]);

    $sql_avis = "DELETE FROM avis WHERE trajet_id IN (SELECT trajet_id FROM trajet WHERE voiture_id = ?)";
    $pdo->prepare($sql_avis)->execute([$id_voiture]);

    $sql_res = "DELETE FROM reservation WHERE trajet_id IN (SELECT trajet_id FROM trajet WHERE voiture_id = ?)";
    $pdo->prepare($sql_res)->execute([$id_voiture]);

    $sql_trajets = "DELETE FROM trajet WHERE voiture_id = ?";
    $pdo->prepare($sql_trajets)->execute([$id_voiture]);

    $sql_voiture = "DELETE FROM voiture WHERE voiture_id = ?";
    $pdo->prepare($sql_voiture)->execute([$id_voiture]);

    $pdo->commit();
    header("Location: profil.php?succes=suppression_complete");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("ERREUR SQL : " . $e->getMessage());
}