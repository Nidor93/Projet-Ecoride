<?php
session_start();
require_once '../db_connect.php';

$user_id = $_SESSION['utilisateur_id'] ?? null;
$trajet_id = $_POST['trajet_id'] ?? null;
$chauffeur_id = $_POST['chauffeur_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if ($user_id && $trajet_id && !empty($message)) {
    $stmt = $pdo->prepare("INSERT INTO messagerie (trajet_id, expediteur_id, destinataire_id, contenu, date_envoi) VALUES (?, ?, ?, ?, NOW())");
    if ($stmt->execute([$trajet_id, $user_id, $chauffeur_id, $message])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'invalid']);
}