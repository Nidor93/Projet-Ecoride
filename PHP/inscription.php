<?php
session_start();
require_once '../db_connect.php';

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $sexe = $_POST['sexe'] ?? '';
    $telephone = $_POST['telephone'];
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
                $sql = "INSERT INTO utilisateur (nom, prenom, sexe, email, password, credit, role, telephone) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $nom,
                    $prenom,
                    $sexe,
                    $email,
                    $hashedPassword,
                    20,
                    'utilisateur',
                    $telephone
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
<?php include('../components/header.php') ?>

<body class="d-flex flex-column min-vh-100">

<?php include('../components/nav.php') ?>

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
            <?php include("../form/inscription_form.html"); ?>
            

            <div class="text-center mt-4">
                <p>Vous avez déjà un compte ? <a href="connexion.php" class="text-success fw-bold text-decoration-none">Se connecter</a></p>
            </div>
        </div>
    </div>
</section>

<?php include("../components/footer.html"); ?>

</body>