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
<?php include('components/header.php') ?>

<body class="d-flex flex-column min-vh-100">

<?php include('components/nav.php') ?>

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

<?php include("components/footer.html"); ?>

</body>
</html>