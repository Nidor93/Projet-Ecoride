<?php
session_start();
require_once 'db_connect.php';

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);

    $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $message = "Un email de récupération a été envoyé à l'adresse indiquée (si celle-ci est valide).";
        $messageClass = "alert-success";
    } else {
        $message = "Si ce compte existe, un email de récupération a été envoyé.";
        $messageClass = "alert-info";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EcoRide - Récupération de mot de passe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
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

<section class="login-background py-5">
    <div class="container">
        <div class="logregis-card mx-auto p-4 bg-white shadow rounded" style="max-width: 500px;">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-success">Mot de passe oublié ?</h2>
                <p class="text-muted">Veuillez renseigner votre mail pour recevoir un lien de réinitialisation.</p>
            </div>

            <?php if ($message !== ""): ?>
                <div class="alert <?php echo $messageClass; ?> shadow-sm">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="new-password.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Adresse Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="nom@exemple.com" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success fw-bold">Envoyer un mail de récupération</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <a href="connexion.php" class="text-muted small">Retour à la connexion</a>
            </div>
        </div>
    </div>
</section>

<footer class="bg-success text-white text-center py-3 mt-auto">
    <p class="mb-1">contact@ecoride.fr</p>
    <a href="mentions-legales.php" class="text-white text-decoration-underline">Mentions légales</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>