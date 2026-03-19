<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['utilisateur_id'];

$stmt = $pdo->prepare("SELECT m.trajet_id, m.contenu, m.date_envoi, m.est_lu,t.ville_depart, t.ville_arrivee, u.prenom AS interlocuteur_nom
                       FROM messagerie m
                       JOIN trajet t ON m.trajet_id = t.trajet_id
                       JOIN utilisateur u ON (u.utilisateur_id = m.expediteur_id OR u.utilisateur_id = m.destinataire_id)
                       WHERE (m.expediteur_id = :user1 OR m.destinataire_id = :user2)
                       AND u.utilisateur_id != :user3
                       GROUP BY m.trajet_id
                       ORDER BY m.date_envoi DESC");
$stmt->execute(['user1' => $user_id,
                'user2' => $user_id,
                'user3' => $user_id]);
$conversations = $stmt->fetchAll();
?>

<?php include('../components/header.php') ?>
<body class="d-flex flex-column min-vh-100 bg-light">
<?php include('../components/nav.php') ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4 fw-bold text-success"><i class="bi bi-chat-dots-fill me-2"></i>Mes Conversations</h2>

            <?php if (count($conversations) > 0): ?>
                <div class="list-group shadow-sm">
                    <?php foreach ($conversations as $conv): ?>
                        <a href="chat.php?trajet_id=<?= $conv['trajet_id'] ?>" 
                           class="list-group-item list-group-item-action p-3 <?= ($conv['est_lu'] == 0) ? 'bg-light border-start border-success border-4' : '' ?>">
                            
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h5 class="mb-1 fw-bold text-primary">
                                    <?= htmlspecialchars($conv['interlocuteur_nom']) ?> 
                                    <small class="text-muted fw-normal">pour le trajet</small>
                                </h5>
                                <small class="text-muted"><?= date('d/m H:i', strtotime($conv['date_envoi'])) ?></small>
                            </div>

                            <p class="mb-1 text-dark fw-semibold">
                                <?= htmlspecialchars($conv['ville_depart']) ?> <i class="bi bi-arrow-right"></i> <?= htmlspecialchars($conv['ville_arrivee']) ?>
                            </p>
                            
                            <small class="text-muted italic">
                                <?= (strlen($conv['contenu']) > 60) ? substr(htmlspecialchars($conv['contenu']), 0, 60) . '...' : htmlspecialchars($conv['contenu']) ?>
                            </small>

                            <?php if ($conv['est_lu'] == 0): ?>
                                <span class="badge bg-success float-end">Nouveau</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <i class="bi bi-chat-left-text text-muted display-1"></i>
                    <p class="mt-3 text-muted">Vous n'avez pas encore de messages.</p>
                    <a href="recherche.php" class="btn btn-success">Trouver un trajet</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../components/footer.html') ?>
</body>