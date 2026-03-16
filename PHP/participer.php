<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['id'])) {
    header('Location: recherche.php');
    exit;
}

$user_id = $_SESSION['utilisateur_id'];
$trajet_id = intval($_GET['id']);
$frais_service = 2;

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT t.nb_place, t.prix, u.credit 
                           FROM trajet t 
                           CROSS JOIN utilisateur u
                           WHERE t.trajet_id = ? AND u.utilisateur_id = ?
                           FOR UPDATE");
    $stmt->execute([$trajet_id, $user_id]);
    $data = $stmt->fetch();

    if (!$data) {
        throw new Exception("Données introuvables.");
    }

$cout_total = $data['prix'] + $frais_service;

    if ($data['nb_place'] > 0 && $data['credit'] >= $cout_total) {

        $upd_user = $pdo->prepare("UPDATE utilisateur SET credit = credit - ? WHERE utilisateur_id = ?");
        $upd_user->execute([$cout_total, $user_id]);

        $upd_trajet = $pdo->prepare("UPDATE trajet SET nb_place = nb_place - 1 WHERE trajet_id = ?");
        $upd_trajet->execute([$trajet_id]);

        $ins_res = $pdo->prepare("INSERT INTO reservation (utilisateur_id, trajet_id, date_reservation) 
                          VALUES (:user, :trajet, NOW())");

        $ins_res->execute([
        'user'   => $user_id,
        'trajet' => $trajet_id
        ]);
        $pdo->commit();
        header('Location: profil.php?success=reservation');
        exit;

    } else {
        $pdo->rollBack();
        $error = ($data['nb_place'] <= 0) ? 'complet' : 'insuffisant';
        header('Location: details.php?id='.$trajet_id.'&error=' .$error);
        exit;
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Erreur lors de la transaction : " . $e->getMessage());
}