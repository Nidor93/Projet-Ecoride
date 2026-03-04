<?php
session_start();
require_once 'db_connect.php';

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $sexe = $_POST['sexe'] ?? '';
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if ($password !== $confirm_password) {
        $message = "Les mots de passe ne correspondent pas.";
        $messageClass = "alert-danger";
    } else {
        $checkEmail = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = ?");
        $checkEmail->execute([$email]);
        
        if ($checkEmail->fetch()) {
            $message = "Cette adresse email est déjà utilisée.";
            $messageClass = "alert-danger";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                $sql = "INSERT INTO utilisateur (nom, prenom, sexe, email, password, credit, role) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $nom,
                    $prenom,
                    $sexe,
                    $email,
                    $hashedPassword,
                    20,
                    'utilisateur'
                ]);
                $message = "Inscription réussie ! Vous avez reçu 20 crédits offerts. <a href='connexion.php'>Connectez-vous ici</a>";
                $messageClass = "alert-success";
            } catch (PDOException $e) {
                $message = "Erreur lors de l'inscription : " . $e->getMessage();
                $messageClass = "alert-danger";
            }
        }
    }
}
?>
<?php include('components/header.php') ?>

<body class="d-flex flex-column min-vh-100">

<?php include('components/nav.php') ?>

<section class="register-background py-5">
    <div class="container">
        <div class="logregis-card mx-auto p-4 bg-white shadow rounded" style="max-width: 600px;">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-success">Créer un compte</h2>
                <p class="text-muted">Rejoignez la communauté EcoRide et recevez 20 crédits</p>
            </div>

            <?php if ($message !== ""): ?>
                <div class="alert <?php echo $messageClass; ?> shadow-sm">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="inscription.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label fw-bold">Nom</label>
                        <input type="text" name="nom" class="form-control" id="nom" placeholder="Votre nom" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label fw-bold">Prénom</label>
                        <input type="text" name="prenom" class="form-control" id="prenom" placeholder="Votre prénom" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="sexe" class="form-label fw-bold">Sexe</label>
                    <select name="sexe" class="form-select" id="sexe" required>
                        <option value="" selected disabled>Choisissez...</option>
                        <option value="H">Homme</option>
                        <option value="F">Femme</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Adresse Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="nom@exemple.com" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label fw-bold">Mot de passe</label>
                        <input type="password" name="password" class="form-control" id="password" required minlength="12" pattern=".*[^\w\s].*" title="Minimum 12 caractères et un caractère spécial.">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirm-password" class="form-label fw-bold">Confirmer Mot de passe</label>
                        <input type="password" name="confirm-password" class="form-control" id="confirm-password" required>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="cgu" required>
                    <label class="form-check-label small" for="cgu">
                        J'accepte les <a href="mentions-legales.php" class="text-success text-decoration-underline">Conditions Générales d'Utilisation</a>
                    </label>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-success fw-bold">S'inscrire</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p>Vous avez déjà un compte ? <a href="connexion.php" class="text-success fw-bold text-decoration-none">Se connecter</a></p>
            </div>
        </div>
    </div>
</section>

<?php include("components/footer.html"); ?>

</body>
</html>