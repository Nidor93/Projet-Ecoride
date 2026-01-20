<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['nouvelle_photo'])) {
    $user_id = $_SESSION['utilisateur_id'];
    $file = $_FILES['nouvelle_photo'];

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($extension, $allowed_extensions)) {
        
        $new_filename = "profil_" . $user_id . "_" . time() . "." . $extension;
        $destination = "Image/" . $new_filename;

        if (!is_dir('Image')) {
            mkdir('Image', 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $sql = "UPDATE utilisateur SET photo_profil = ? WHERE utilisateur_id = ?";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$new_filename, $user_id])) {
                header('Location: profil.php?success=1');
            } else {
                header('Location: profil.php?error=sql_error');
            }
        } else {
            header('Location: profil.php?error=upload_failed');
        }
    } else {
        header('Location: profil.php?error=invalid_format');
    }
    exit();
}