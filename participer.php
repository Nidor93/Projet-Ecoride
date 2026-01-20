<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['id'])) {
    header('Location: recherche.php');
    exit;
}

$user_id = $_SESSION['utilisateur_id'];
$trajet_id = intval($_GET['id']);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT t.nb_place, u.credit FROM trajet t 
                           JOIN utilisateur u ON u.utilisateur_id = ? 
                           WHERE t.trajet_id = ? FOR UPDATE");
    $stmt->execute([$user_id, $trajet_id]);
    $data = $stmt->fetch();

    if (!$data) {
        throw new Exception("Données introuvables.");
    }

    if ($data['nb_place'] > 0 && $data['credit'] >= 2) {

        $upd_user = $pdo->prepare("UPDATE utilisateur SET credit = credit - 2 WHERE utilisateur_id = ?");
        $upd_user->execute([$user_id]);

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
        header('Location: details.php?id='.$trajet_id.'&error=insuffisant');
        exit;
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Erreur lors de la transaction : " . $e->getMessage());
}