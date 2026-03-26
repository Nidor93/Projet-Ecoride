<div class="card border-0 shadow-sm p-4 border-bottom border-success border-4 mb-4">
                <h4 class="fw-bold text-success mb-3 border-bottom pb-2">Mes réservations (Passager)</h4>
                <?php if (count($mes_participations) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Itinéraire</th>
                                    <th>Chauffeur</th>
                                    <th>Statut</th>
                                    <th>Détails</th>
                                    <th>Annulation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mes_participations as $res): ?>
                                    <tr>
                                        <td class="small"><?php echo date('d/m/Y', strtotime($res['date_depart'])); ?></td>
                                        <td><strong><?php echo htmlspecialchars($res['ville_depart']); ?></strong> → <strong><?php echo htmlspecialchars($res['ville_arrivee']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($res['chauffeur_nom']); ?></td>
                                        <td class="fw-bold">
                                            <?php if ($res['statut'] == 'attente'): ?>
                                                <p class="mb-1">En attente</p>

                                            <?php elseif ($res['statut'] == 'en_cours'): ?>
                                                <p class="mb-1">Voyage en cours</p>

                                            <?php elseif ($res['statut'] == 'termine'): ?>
                                                <p class="mb-1">Trajet clos</p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold text-success">
                                            <a href="details_reservation.php?id=<?php echo $res['trajet_id']; ?>" class="btn btn-success btn-sm w-100 fw-bold"> Détails </a>
                                        </td>
                                        <td>
                                            <?php if ($res['statut'] === 'attente'): ?>
                                                <a href="supprimer_trajet.php?id=<?php echo $res['trajet_id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Voulez-vous vraiment annuler votre réservation ? (2 crédits seront prélevés de votre compte)')">
                                                   Annuler
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-lock text-muted">
                                                    <i class="bi bi-patch-check"></i> Annulation impossible
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted small">Vous n'avez pas encore réservé de voyage.</p>
                <?php endif; ?>
            </div>