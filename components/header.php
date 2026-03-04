<?php
$page = basename($_SERVER['PHP_SELF']); 

switch ($page) {
    case 'index.php':
        $titre = "EcoRide - Covoiturage écologique";
        break;
    case 'recherche.php':
        $titre = "EcoRide - Rechercher un trajet";
        break;
    case 'contact.php':
        $titre = "EcoRide - Contact";
        break;
    case 'connexion.php':
        $titre = "EcoRide - Connexion";
        break;
    case  'details.php':
        $titre = "EcoRide - Détails du voyage";
        break;
    case 'inscription.php':
        $titre = "EcoRide - Inscription";
        break;
    case 'mentions-legales.php':
        $titre = "EcoRide - Mentions Légales";
        break;
    case 'new-password.php':
        $titre = "EcoRide - Récupération de mot de passe";
        break;
    case 'profil.php':
        $titre = "EcoRide - Mon Profil";
        break;
    case 'profil_admin.php':
        $titre = "Espace Administrateur - Modération";
        break;
    case 'profil_employe.php':
        $titre = "Espace Employé - Modération";
        break;
    default:
        $titre = "EcoRide";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titre; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>