<?php
session_start();
require_once 'db_connect.php';

if (isset($_GET['id']) && isset($_GET['action']) && isset($_SESSION['utilisateur_id'])) {
    $trajet_id = intval($_GET['id']);
    $action = $_GET['action'];
    $chauffeur_id = $_SESSION['utilisateur_id'];

    if ($action === 'demarrer') {
        $nouveau_statut = 'en_cours';
    } elseif ($action === 'clore') {
        $nouveau_statut = 'termine';
    } else {
        header('Location: profil.php');
        exit;
    }

    $stmt = $pdo->prepare("UPDATE trajet 
                           SET statut = ?, nb_place = 0 
                           WHERE trajet_id = ? AND chauffeur_id = ?");
    
    $stmt->execute([$nouveau_statut, $trajet_id, $chauffeur_id]);

    header('Location: profil.php?succes=statut_ajour');
    exit;
} else {
    header('Location: profil.php');
    exit;
}