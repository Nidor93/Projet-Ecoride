<form action="../PHP/creer_trajet.php" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ville de départ</label>
                    <input type="text" name="ville_depart" class="form-control" placeholder="Ex: Paris" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ville d'arrivée</label>
                    <input type="text" name="ville_arrivee" class="form-control" placeholder="Ex: Lyon" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Date du voyage</label>
                    <input type="date" name="date_depart" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Heure de départ</label>
                    <input type="time" name="heure_depart" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Heure d'arrivée</label>
                    <input type="time" name="heure_arrivee" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Prix par passager</label>
                    <input type="number" name="prix" class="form-control" step="0.50" placeholder="Ex: 25" required>
                    <div class="form-text text-danger">2 crédits seront prélevés par la plateforme sur ce prix.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nombre de places</label>
                    <input type="number" name="nb_place" class="form-control" min="1" max="8" required>

                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Choisir votre véhicule</label>
                    <select name="voiture_id" class="form-select" required>
                        <option value="">-- Sélectionnez un véhicule --</option>
                        <?php foreach ($mes_voitures as $v): ?>
                            <option value="<?php echo $v['voiture_id']; ?>">
                                <?php echo htmlspecialchars($v['modele'] . " (" . $v['immatriculation'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">Publier mon trajet</button>
                </div>
            </div>
</form>