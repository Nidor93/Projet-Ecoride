<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $chauffeur_id = $_SESSION['utilisateur_id'];
    $ville_depart = trim($_POST['ville_depart']);
    $ville_arrivee = trim($_POST['ville_arrivee']);
    $date_depart = $_POST['date_depart'];
    $heure_depart = $_POST['heure_depart'];
    $heure_arrivee = $_POST['heure_arrivee'];
    $prix = floatval($_POST['prix']);
    $nb_place = intval($_POST['nb_place']);
    $voiture_id = intval($_POST['voiture_id']);

    if (empty($ville_depart) || empty($ville_arrivee) || $prix <= 0 || $nb_place <= 0) {
        header('Location: profil.php?error=champs_invalides');
        exit;
    }

    try {
        $sql = "INSERT INTO trajet (
                    chauffeur_id, 
                    ville_depart, 
                    ville_arrivee, 
                    date_depart, 
                    heure_depart, 
                    heure_arrivee, 
                    prix, 
                    nb_place, 
                    voiture_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $chauffeur_id,
            $ville_depart,
            $ville_arrivee,
            $date_depart,
            $heure_depart,
            $heure_arrivee,
            $prix,
            $nb_place,
            $voiture_id
        ]);

        header('Location: profil.php?success=trajet_cree');
        exit;

    } catch (PDOException $e) {
        die("Erreur lors de la création du trajet : " . $e->getMessage());
    }
} else {
    header('Location: profil.php');
    exit;
}