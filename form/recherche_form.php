<form method="GET" action="recherche.php" class="d-flex flex-column">
                    <input type="hidden" name="depart" value="<?php echo htmlspecialchars($_GET['depart'] ?? ''); ?>">
                    <input type="hidden" name="arrivee" value="<?php echo htmlspecialchars($_GET['arrivee'] ?? ''); ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Prix Max</label>
                        <div class="input-group">
                            <input type="number" name="prix_max" class="form-control" value="<?php echo htmlspecialchars($_GET['prix_max'] ?? ''); ?>">
                            <span class="input-group-text">€</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Durée Max (heures)</label>
                        <div class="input-group">
                            <input type="number" name="duree_max_h" step="0.5" min="0" class="form-control" 
                                   value="<?php echo htmlspecialchars($_GET['duree_max_h'] ?? ''); ?>" placeholder="Ex: 3">
                            <span class="input-group-text">h</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Note minimale</label>
                        <select name="etoiles_min" class="form-select">
                            <option value="0">Toutes</option>
                            <?php for($i=1; $i<=5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($_GET['etoiles_min'] ?? '') == $i ? 'selected' : ''; ?>><?php echo $i; ?>+ ★</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="eco_only" id="eco" value="1" <?php echo isset($_GET['eco_only']) ? 'checked' : ''; ?>>
                        <label class="form-check-label small" for="eco">Électrique uniquement</label>
                    </div>

                    <button type="submit" class="btn btn-success fw-bold w-100 mb-2">Appliquer</button>
                    <a href="recherche.php?depart=<?php echo urlencode($_GET['depart'] ?? ''); ?>&arrivee=<?php echo urlencode($_GET['arrivee'] ?? ''); ?>" class="btn btn-link btn-sm text-secondary text-decoration-none text-center">Réinitialiser</a>
</form>