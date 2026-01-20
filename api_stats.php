<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$sql = "SELECT date_depart, COUNT(*) as nb_trajets, SUM(prix) as gains 
        FROM trajet 
        GROUP BY date_depart 
        ORDER BY date_depart ASC 
        LIMIT 7";

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dates = [];
$nb_trajets = [];
$gains = [];

foreach ($data as $row) {
    $dates[] = $row['date_depart'];
    $nb_trajets[] = $row['nb_trajets'];
    $gains[] = $row['gains'];
}

echo json_encode([
    'labels' => $dates,
    'trajets' => $nb_trajets,
    'gains' => $gains
]);