<?php
$host = getenv('DB_HOST') ?: 'mysql-ecoride-mat.alwaysdata.net'; 
$dbname = getenv('DB_NAME') ?: 'ecoride-mat_votre_bdd';
$user = getenv('DB_USER') ?: 'ecoride-mat';
$pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>