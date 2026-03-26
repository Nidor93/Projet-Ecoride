<div class="card border-0 shadow-sm p-4 bg-white border-bottom border-success border-4 mb-4">
                    <h4 class="fw-bold text-success mb-3">Véhicule Principal</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Modèle :</strong> <?php echo htmlspecialchars($user['modele']); ?></p>
                            <p class="mb-1"><strong>Immatriculation :</strong> <?php echo htmlspecialchars($user['immatriculation']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <span class="badge <?php echo $user['pref_fumeur'] ? 'bg-success' : 'bg-secondary'; ?>">Fumeur : <?php echo $user['pref_fumeur'] ? 'OK' : 'NON'; ?></span>
                            <span class="badge <?php echo $user['pref_animal'] ? 'bg-success' : 'bg-secondary'; ?>">Animaux : <?php echo $user['pref_animal'] ? 'OK' : 'NON'; ?></span>
                            <?php if (!empty($v['categorie'])): ?>
                                    <p class="mt-3 mb-0 small text-muted italic">
                                        <i class="bi bi-chat-dots"></i> "<?php echo htmlspecialchars($v['categorie']); ?>"
                                    </p>
                            <?php endif; ?>
                            <div class="mt-2 text-end">
                                <a href="supprimer_voiture.php?id=<?php echo $mes_voitures[0]['voiture_id']; ?>" 
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Voulez-vous vraiment supprimer votre véhicule ?')">
                                    Supprimer Véhicule
                                </a>
                            </div>
                            <div class="mt-2 text-end">
                                <button class="btn btn-sm btn-success" data-bs-toggle="collapse" data-bs-target="#collapseVoitureUpdate">Changer les informations du véhicule ?</button>
                            </div>
                        </div>
                        <div class="collapse" id="collapseVoitureUpdate">
                            <div class="card card-body border-0 shadow-sm mb-4">
                                <h5 class="fw-bold text-success mb-3">Mise à jour des informations du véhicule</h5>
                                <?php include("../form/maj_infos_voiture_form.html"); ?>
                            </div>
                        </div>
                    </div>
                </div>