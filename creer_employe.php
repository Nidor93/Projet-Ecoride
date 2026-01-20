<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password_brut = $_POST['password'];

    if (empty($nom) || empty($prenom) || empty($email) || empty($password_brut)) {
        header('Location: profil_admin.php?error=champs_vides');
        exit();
    }

    $check_stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = ?");
    $check_stmt->execute([$email]);
    
    if ($check_stmt->fetch()) {
        header('Location: profil_admin.php?error=email_existant');
        exit();
    }

    $password_hash = password_hash($password_brut, PASSWORD_DEFAULT);
    $role = 'employe';

    try {
        $sql = "INSERT INTO utilisateur (nom, prenom, email, password, role, est_suspendu) 
                VALUES (?, ?, ?, ?, ?, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $email, $password_hash, $role]);

        header('Location: profil_admin.php?success=employe_cree');
        exit();

    } catch (PDOException $e) {
        header('Location: profil_admin.php?error=db_error');
        exit();
    }
} else {
    header('Location: profil_admin.php');
    exit();
}