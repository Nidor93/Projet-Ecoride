<div class="card-body">
            <?php if (count($incidents) > 0): ?>
                <div class="row">
                    <?php foreach ($incidents as $incident): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-start border-danger border-4 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="fw-bold text-danger">Trajet #<?php echo $incident['trajet_id']; ?></h6>
                                        <span class="badge bg-danger"><?php echo $incident['note']; ?> / 5 ★</span>
                                    </div>
                                    
                                    <div class="mt-2 small">
                                        <p class="mb-1"><strong>Départ :</strong> <?php echo htmlspecialchars($incident['ville_depart']); ?> le <?php echo date('d/m/Y', strtotime($incident['date_depart'])); ?> à <?php echo $incident['heure_depart']; ?></p>
                                        <p class="mb-1"><strong>Destination :</strong> <?php echo htmlspecialchars($incident['ville_arrivee']); ?></p>
                                    </div>

                                    <hr>
                                    
                                    <p class="mb-0 mt-2 small">
                                        <strong>Avis de <?php echo htmlspecialchars($incident['passager_nom']); ?> :</strong><br>
                                        <span class="fst-italic text-muted">"<?php echo htmlspecialchars($incident['commentaire']); ?>"</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success fs-1"></i>
                    <p class="text-muted mt-2">Aucun covoiturage contestable à signaler.</p>
                </div>
            <?php endif; ?>
        </div>