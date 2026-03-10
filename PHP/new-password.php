<?php
session_start();
require_once '../db_connect.php';

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
<?php include('../components/header.php') ?>

<body class="d-flex flex-column min-vh-100">

<?php include('../components/nav.php') ?>

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

<?php include("../components/footer.html"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>