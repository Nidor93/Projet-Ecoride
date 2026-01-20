<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EcoRide - Mentions Légales</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .legal-box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-top: 30px;
            margin-bottom: 50px;
        }
        h2 { color: #198754; font-size: 1.5rem; margin-top: 25px; border-bottom: 2px solid #e6f4ea; padding-bottom: 10px; }
        p, li { color: #555; line-height: 1.6; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">EcoRide</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
               <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
               <li class="nav-item"><a class="nav-link" href="recherche.php">Accès aux Covoiturages</a></li>
            <?php if (isset($_SESSION['utilisateur_id'])): ?>
               <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
               <li class="nav-item"><a class="nav-link text-warning" href="deconnexion.php">Déconnexion</a></li>
            <?php else: ?>
               <li class="nav-item"><a class="nav-link active fw-bold" href="connexion.php">Connexion</a></li>
               <li class="nav-item"><a class="nav-link" href="inscription.php">Inscription</a></li>
            <?php endif; ?>
               <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="main-content container">
    <div class="legal-box">
        <h1 class="text-center fw-bold mb-4 text-success">Mentions Légales</h1>
        <p class="text-center text-muted">En vigueur au 1er Janvier <?php echo date('Y'); ?></p>

        <section>
            <h2>1. Éditeur du site</h2>
            <p>Le site <strong>EcoRide</strong> est édité par la société EcoRide SAS, au capital de 10 000 euros, immatriculée au RCS de Paris sous le numéro 123 456 789.</p>
            <ul>
                <li><strong>Siège social :</strong> 12 rue de l'Écologie, 75000 Paris</li>
                <li><strong>Directeur de la publication :</strong> José (Fondateur)</li>
                <li><strong>Contact :</strong> contact@ecoride.fr | 01 23 45 67 89</li>
            </ul>
        </section>

        <section>
            <h2>2. Hébergement</h2>
            <p>Le site est hébergé par la société <strong>EcoCloud Hosting</strong>.</p>
            <ul>
                <li><strong>Adresse :</strong> 45 Avenue du Numérique, 69000 Lyon</li>
                <li><strong>Site web :</strong> www.ecocloud.fr</li>
            </ul>
        </section>

        <section>
            <h2>3. Propriété intellectuelle</h2>
            <p>L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques.</p>
        </section>

        <section>
            <h2>4. Protection des données (RGPD)</h2>
            <p>Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez d'un droit d'accès, de rectification et de suppression des données vous concernant. Vous pouvez exercer ce droit en envoyant un e-mail à : <strong>dpo@ecoride.fr</strong>.</p>
            <p>Les données collectées (nom, prénom, email, plaque d'immatriculation) sont nécessaires au bon fonctionnement du service de covoiturage et à la gestion des crédits.</p>
        </section>

        <section>
            <h2>5. Cookies</h2>
            <p>Le site utilise des cookies de session pour permettre la connexion à votre espace utilisateur et faciliter la navigation. Ces cookies ne sont pas utilisés à des fins publicitaires.</p>
        </section>

        <div class="text-center mt-5">
            <a href="index.php" class="btn btn-outline-success px-4">Retour à l'accueil</a>
        </div>
    </div>
</main>

<footer class="bg-success text-white text-center py-3 mt-auto">
    <div class="container">
        <p class="mb-0 small">&copy; <?php echo date('Y'); ?> EcoRide - Tous droits réservés</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>