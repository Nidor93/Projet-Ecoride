<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['trajet_id'])) {
    header('Location: messagerie.php');
    exit;
}

$user_id = $_SESSION['utilisateur_id'];
$trajet_id = intval($_GET['trajet_id']);

$stmt_info = $pdo->prepare("SELECT t.ville_depart, t.ville_arrivee, u.sexe, u.prenom, u.photo_profil, u.utilisateur_id AS interlocuteur_id
                            FROM trajet t
                            JOIN messagerie m ON t.trajet_id = m.trajet_id
                            JOIN utilisateur u ON (u.utilisateur_id = m.expediteur_id OR u.utilisateur_id = m.destinataire_id)
                            WHERE t.trajet_id = ? AND u.utilisateur_id != ?
                            LIMIT 1
");
$stmt_info->execute([$trajet_id, $user_id]);
$info = $stmt_info->fetch();

if (!$info) {
    header('Location: messagerie.php');
    exit;
}

$stmt_read = $pdo->prepare("UPDATE messagerie SET est_lu = 1 WHERE trajet_id = ? AND destinataire_id = ?");
$stmt_read->execute([$trajet_id, $user_id]);

$stmt_msg = $pdo->prepare("SELECT * FROM messagerie 
                           WHERE trajet_id = ? 
                           AND (expediteur_id = ? OR destinataire_id = ?)
                           ORDER BY date_envoi ASC");
$stmt_msg->execute([$trajet_id, $user_id, $user_id]);
$messages = $stmt_msg->fetchAll();
?>

<?php include('../components/header.php') ?>

<body class="bg-light">

<?php include('../components/nav.php') ?>

<div class="container mt-4">
    <div class="mb-4">
        <a href="javascript:history.back()" class="btn btn-outline-success btn-sm">
            ← Retour à la messagerie
        </a>
    </div>
    <div class="card shadow-sm border-0 m-4">
        <div class="card-header bg-white py-3 d-flex align-items-center">
            <?php
            $default = ($info['sexe'] == 'F') ? '../Image/ProfilF.png' : (($info['sexe'] == 'H') ? '../Image/ProfilM.png' : '../Image/VoitureEcoride.png');
            $img = (!empty($info['photo_profil']) && file_exists("../Image/" . $info['photo_profil'])) ? "../Image/" . $info['photo_profil'] : "../Image/" . $default;
            ?>
            <img src="<?php echo $img; ?>" class="rounded-circle img-avatar mb-2 m-2">
            <div>
                <h5 class="mb-0 fw-bold"><?= htmlspecialchars($info['prenom']) ?></h5>
                <small class="text-muted"><?= htmlspecialchars($info['ville_depart']) ?> → <?= htmlspecialchars($info['ville_arrivee']) ?></small>
            </div>
        </div>

        <div id="chatBox" class="card-body chat-container bg-white" 
             data-trajet="<?= $trajet_id ?>" 
             data-user="<?= $user_id ?>" 
             data-lastid="<?= !empty($messages) ? end($messages)['message_id'] : 0 ?>">
    
            <?php foreach ($messages as $m): ?>
                <?php $isMe = ($m['expediteur_id'] == $user_id); ?>
                <div class="msg <?= $isMe ? 'msg-me' : 'msg-them' ?>">
                    <?= nl2br(htmlspecialchars($m['contenu'])) ?>
                    <span class="msg-date"><?= date('H:i', strtotime($m['date_envoi'])) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card-footer bg-white p-3">
            <form id="chatForm" class="d-flex">
                <input type="hidden" id="chauffeur_id" value="<?= $info['interlocuteur_id'] ?>">
                <input type="text" id="messageInput" class="form-control me-2" placeholder="Écrivez votre message..." required autocomplete="off" autofocus>
                <button type="submit" class="btn btn-success"><i class="bi bi-send"></i>→</button>
            </form>
        </div>
    </div>
</div>

<script src="../JS/chat.js"></script>

<?php include('../components/footer.html') ?>
</body>