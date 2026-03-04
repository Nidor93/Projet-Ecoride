<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">EcoRide</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
               <li class="nav-item"><a class="nav-link <?= ($page == 'index.php') ? 'active fw-bold' : ''; ?>" href="index.php">Accueil</a></li>
               <li class="nav-item"><a class="nav-link <?= ($page == 'recherche.php') ? 'active fw-bold' : ''; ?>" href="recherche.php">Accès aux Covoiturages</a></li>
            <?php if (isset($_SESSION['utilisateur_id'])): ?>
               <li class="nav-item"><a class="nav-link <?= ($page == 'profil.php') ?'active fw-bold' : ''; ?>" href="profil.php">Mon Profil</a></li>
               <li class="nav-item"><a class="nav-link text-warning" href="deconnexion.php">Déconnexion</a></li>
            <?php else: ?>
               <li class="nav-item"><a class="nav-link <?=  ($page == 'connexion.php') ? 'active fw-bold' : ''; ?>" href="connexion.php">Connexion</a></li>
               <li class="nav-item"><a class="nav-link <?=  ($page == 'inscription.php') ? 'active fw-bold' : ''; ?>" href="inscription.php">Inscription</a></li>
            <?php endif; ?>
               <li class="nav-item"><a class="nav-link <?= ($page == 'contact.php') ? 'active fw-bold' : ''; ?>" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

