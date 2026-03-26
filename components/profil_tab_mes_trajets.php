<div class="card border-0 shadow-sm p-4 border-bottom border-success border-4 mb-4">
                <h4 class="fw-bold text-success mb-3 border-bottom pb-2">Mes trajets mis en ligne</h4>
                <?php if (count($mes_trajets_proposes) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Itinéraire</th>
                                    <th>Places</th>
                                    <th>Prix</th>
                                    <th>Annulation</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mes_trajets_proposes as $tr): ?>
                                    <tr>
                                        <td class="small"><?php echo date('d/m/Y', strtotime($tr['date_depart'])); ?></td>
                                        <td><strong><?php echo htmlspecialchars($tr['ville_depart']); ?></strong> → <strong><?php echo htmlspecialchars($tr['ville_arrivee']); ?></strong></td>
                                        <td><span class="badge bg-info"><?php echo $tr['nb_place']; ?> restantes</span></td>
                                        <td class="fw-bold"><?php echo number_format($tr['prix'], 2); ?> €</td>
                                        <td>
                                            <?php if ($tr['statut'] === 'attente'): ?>
                                                <a href="supprimer_trajet.php?id=<?php echo $tr['trajet_id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Voulez-vous vraiment annuler votre course ?')">
                                                   Annuler
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-lock text-muted">
                                                    <i class="bi bi-patch-check"></i> Annulation impossible
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($tr['statut'] == 'attente'): ?>
                                                <a href="modifier_statut_trajet.php?id=<?php echo $tr['trajet_id']; ?>&action=demarrer" 
                                                   class="btn btn-success btn-sm w-100 fw-bold"
                                                   onclick="return confirm('Voulez-vous démarrer la course ?')">
                                                   Démarrer
                                                </a>

                                            <?php elseif ($tr['statut'] == 'en_cours'): ?>
                                                <a href="modifier_statut_trajet.php?id=<?php echo $tr['trajet_id']; ?>&action=clore" 
                                                   class="btn btn-primary btn-sm w-100 fw-bold"
                                                   onclick="return confirm('Êtes-vous arrivé à destination ?')">
                                                   Arrivée à destination
                                                </a>

                                            <?php elseif ($tr['statut'] == 'termine'): ?>
                                                <span class="badge bg-secondary w-100 p-2">Trajet clos</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted small">Vous n'avez pas encore publié de trajet.</p>
                <?php endif; ?>
            </div>