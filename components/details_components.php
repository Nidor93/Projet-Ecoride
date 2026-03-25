<h2 class="fw-bold text-success mb-4 text-center">Détails du voyage</h2>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 h-100 border-top border-success border-4">
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"></div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo substr($t['heure_depart'], 0, 5); ?> - <?php echo htmlspecialchars($t['ville_depart']); ?></h5>
                        </div>
                    </div>
                    <div class="ms-3 mb-3" style="border-left: 2px dashed #198754; height: 40px; width: 2px;"></div>
                    <div class="d-flex align-items-center">
                        <div class="bg-dark text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"></div>
                        <div>
                            <h5 class="fw-bold mb-0">
                                <?php if(isset($t['heure_arrivee'])) echo substr($t['heure_arrivee'], 0, 5) . ' - '; ?>
                                <?php echo htmlspecialchars($t['ville_arrivee']); ?>
                            </h5>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mt-4">
                    <h6 class="text-muted text-uppercase small fw-bold mb-3">Infos véhicule & Règles à bord</h6>
                    <div class="p-3 bg-light rounded shadow-sm">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Modèle :</strong> <?php echo htmlspecialchars($t['modele'] ?? 'Non renseigné'); ?></p>
                                <p class="mb-1"><strong>Couleur :</strong> <?php echo htmlspecialchars($t['couleur'] ?? '-'); ?></p>
                                <p class="mb-1"><strong>Immatriculation :</strong> <?php echo htmlspecialchars($t['immatriculation'] ?? 'Non renseigné'); ?></p>
                            </div>
                            <div class="col-md-6 border-start border-white">
                                <p class="mb-2"><strong>Préférences :</strong></p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge <?php echo $t['pref_fumeur'] ? 'bg-success' : 'bg-danger'; ?> p-2">
                                        <?php echo $t['pref_fumeur'] ? ' Fumeur accepté' : ' Non-fumeur'; ?>
                                    </span>
                                    <span class="badge <?php echo $t['pref_animal'] ? 'bg-success' : 'bg-danger'; ?> p-2">
                                        <?php echo $t['pref_animal'] ? ' Animaux OK' : ' Pas d\'animaux'; ?>
                                    </span>
                                    <?php if (!empty($t['est_electrique'])): ?>
                                        <span class="badge bg-success-subtle text-success border border-success"> Électrique</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Thermique</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($t['categorie'])): ?>
                                    <p class="mt-3 mb-0 small text-muted italic">
                                        <i class="bi bi-chat-dots"></i> "<?php echo htmlspecialchars($t['categorie']); ?>"
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                    <h5 class="fw-bold mb-3">Avis de nos passagers</h5>
                    <?php if ($dernier_avis): ?>
                        <div class="card p-3 border-0 bg-light shadow-sm">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fw-bold me-2"><?php echo htmlspecialchars($dernier_avis['prenom']); ?></span>
                                <div class="text-warning small">
                                    <?php echo str_repeat('★', $dernier_avis['note']); ?>
                                </div>
                            </div>
                            <p class="mb-0 italic">"<?php echo htmlspecialchars($dernier_avis['commentaire']); ?>"</p>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small">Aucun avis pour le moment.</p>
                    <?php endif; ?>
        </div>
    </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm border-top border-success border-4 p-4 text-center h-100">
                <div class="mb-4">
                    <?php
                            if ($t['sexe'] == 'F') {
                                $default_img = '../Image/ProfilF.png';
                            } elseif ($t['sexe'] == 'H') {
                                $default_img = '../Image/ProfilM.png';
                            } else {
                                $default_img = '../Image/VoitureEcoride.png';
                            }

                            if (!empty($t['photo_profil']) && file_exists("../Image/" . $t['photo_profil'])) {
                                $img_chauffeur = "../Image/" . $t['photo_profil'];
                            } else {
                                $img_chauffeur = "../Image/" . $default_img;
                            }
                            ?>
                            <img src="<?php echo $img_chauffeur; ?>" class="rounded-circle" width="100" height="100" alt="Photo de profil" style="object-fit: cover;">
                    <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($t['prenom'] . ' ' . $t['nom']); ?></h4>
                    <p class="fw-bold mb-1">Téléphone : 0<?php echo $t['telephone']; ?></p>
                    <p class="text-warning mb-0">
                        <?php 
                        $note = $t['note_moyenne'] ? round($t['note_moyenne']) : 0;
                        for($i=1; $i<=5; $i++) echo ($i <= $note) ? '★' : '☆';
                        ?>
                        <span class="text-muted small ms-1">(<?php echo $t['nb_avis']; ?> avis)</span>
                    </p>
                </div>

                <div class="bg-light p-3 rounded mb-4">
                    <span class="text-muted d-block small mb-1">Prix par passager</span>
                    <span class="h2 fw-bold text-success mb-0"><?php echo number_format($t['prix'], 2); ?>€</span>
                </div>
                
                <p class="text-muted mb-4">
                    <i class="bi bi-people"></i> <?php echo $t['nb_place']; ?> places restantes
                </p>