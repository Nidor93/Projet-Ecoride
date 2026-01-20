<?php
session_start();
require_once 'db_connect.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['utilisateur_id'] = $user['utilisateur_id'];
            $_SESSION['role'] = strtolower(trim($user['role']));

            $role = $_SESSION['role'];

            if ($role === 'admin') {
                header('Location: profil_admin.php');
            } elseif ($role === 'employe') {
                header('Location: profil_employe.php');
            } else {
                header('Location: profil.php');
            }
            exit();

        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EcoRide - Connexion</title>
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
<section class="login-background flex-grow-1 d-flex align-items-center">
    <div class="container">
        <div class="logregis-card mx-auto">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-success">Bienvenue</h2>
                <p class="text-muted">Connectez-vous pour commencer à covoiturer</p>
            </div>

            <?php if ($erreur): ?>
                <div class="alert alert-danger shadow-sm py-2 small text-center">
                    <?php echo $erreur; ?>
                </div>
            <?php endif; ?>

            <form action="connexion.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Adresse Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="nom@exemple.com" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Mot de passe</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Votre mot de passe" required>
                </div>


                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-ecoride fw-bold py-2">Se connecter</button>
                </div>
            </form>

            <div class="text-center mt-4 border-top pt-3">
                <p class="mb-1"><a href="new-password.php" class="text-success text-decoration-none small">Mot de passe oublié ?</a></p>
                <p class="small">Pas encore de compte ? <a href="inscription.php" class="text-success fw-bold text-decoration-none">S'inscrire</a></p>
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