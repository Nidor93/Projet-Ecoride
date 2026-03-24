<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

$trajet_id = isset($_GET['trajet_id']) ? intval($_GET['trajet_id']) : 0;
$last_id = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
$user_id = $_SESSION['utilisateur_id'] ?? null;

if (!$user_id || $trajet_id <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT message_id, expediteur_id, contenu, date_envoi 
                       FROM messagerie 
                       WHERE trajet_id = ? 
                       AND message_id > ? 
                       ORDER BY date_envoi ASC");
$stmt->execute([$trajet_id, $last_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);