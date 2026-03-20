<?php
$page = basename($_SERVER['PHP_SELF']);
$non_lus = 0;
if (isset($_SESSION['utilisateur_id'])) {
    $stmt_notif = $pdo->prepare("SELECT COUNT(*) FROM messagerie WHERE destinataire_id = ? AND est_lu = 0");
    $stmt_notif->execute([$_SESSION['utilisateur_id']]);
    $non_lus = $stmt_notif->fetchColumn();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../PHP/index.php">EcoRide</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
               <li class="nav-item"><a class="nav-link <?= ($page == 'index.php') ? 'active fw-bold' : ''; ?>" href="../PHP/index.php">Accueil</a></li>
               <li class="nav-item"><a class="nav-link <?= ($page == 'recherche.php') ? 'active fw-bold' : ''; ?>" href="../PHP/recherche.php">Accès aux Covoiturages</a></li>
            <?php if (isset($_SESSION['utilisateur_id'])): ?>
               <li class="nav-item"><a class="nav-link <?= ($page == 'profil.php') ?'active fw-bold' : ''; ?>" href="../PHP/profil.php">Mon Profil</a></li>
               <li class="nav-item">
                   <a class="nav-link position-relative" href="messagerie.php">
                       <i class="bi bi-chat-dots"></i> Messagerie
                       <?php if ($non_lus > 0): ?>
                           <span class="position-absolute top-10 start-95 translate-middle badge rounded-pill bg-danger">
                               <?= $non_lus ?>
                               <span class="visually-hidden">messages non lus</span>
                           </span>
                       <?php endif; ?>
                   </a>
               </li>
               <li class="nav-item"><a class="nav-link text-warning" href="deconnexion.php">Déconnexion</a></li>
            <?php else: ?>
               <li class="nav-item"><a class="nav-link <?=  ($page == 'connexion.php') ? 'active fw-bold' : ''; ?>" href="../PHP/connexion.php">Connexion</a></li>
               <li class="nav-item"><a class="nav-link <?=  ($page == 'inscription.php') ? 'active fw-bold' : ''; ?>" href="../PHP/inscription.php">Inscription</a></li>
            <?php endif; ?>
               <li class="nav-item"><a class="nav-link <?= ($page == 'contact.php') ? 'active fw-bold' : ''; ?>" href="../PHP/contact.php">Contact</a></li>
               <li class="nav-item"><a class="nav-link <?= ($page == 'compteur_co2.php') ?'active fw-bold' : ''; ?>" href="../PHP/compteur_co2.php">Compteur de Co2</a></li>
            </ul>
        </div>
    </div>
</nav>

