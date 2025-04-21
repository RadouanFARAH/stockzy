<?php require_once 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Modifier le Produit</h1>
        <a href="index.php?controller=product&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <?php
    $flash = getFlashMessage();
    if ($flash) {
        echo '<div class="alert alert-' . ($flash['type'] === 'success' ? 'success' : 'danger') . '">' . $flash['message'] . '</div>';
    }
    ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="index.php?controller=product&action=edit&id=<?php echo $product['id']; ?>" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du produit</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $product['nom']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5"><?php echo $product['description']; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prix" class="form-label">Prix (DH)</label>
                                    <input type="number" class="form-control" id="prix" name="prix" step="0.01" min="0" value="<?php echo $product['prix']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantite_stock" class="form-label">Quantité en stock</label>
                                    <input type="number" class="form-control" id="quantite_stock" name="quantite_stock" min="0" value="<?php echo $product['quantite_stock']; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="seuil_alerte" class="form-label">Seuil d'alerte</label>
                            <input type="number" class="form-control" id="seuil_alerte" name="seuil_alerte" min="0" value="<?php echo $product['seuil_alerte']; ?>" required>
                            <div class="form-text">Une alerte sera affichée lorsque le stock sera inférieur ou égal à cette valeur.</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="image" class="form-label">Image du produit</label>
                            <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                                <div class="mb-2">
                                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['nom']; ?>" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Format recommandé: JPG ou PNG, max 2 Mo. Laissez vide pour conserver l'image actuelle.</div>
                        </div>

                        <div class="mb-3">
                            <label for="categorie_id" class="form-label">Catégorie</label>
                            <select class="form-select" id="categorie_id" name="categorie_id">
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($product['categorie_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo $category['nom']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="fournisseur_id" class="form-label">Fournisseur</label>
                            <?php if (isFournisseur()): ?>
                                <!-- If the user is a Fournisseur, show a disabled input with their name -->
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($fournisseurs[0]['nom']); ?>" disabled>
                                <input type="hidden" name="fournisseur_id" value="<?php echo htmlspecialchars($fournisseurs[0]['id']); ?>">
                            <?php else: ?>
                                <!-- If the user is an Admin, show a dropdown -->
                                <select name="fournisseur_id" id="fournisseur_id" class="form-select">
                                    <option value="">Sélectionnez un fournisseur</option>
                                    <?php foreach ($fournisseurs as $fournisseur): ?>
                                        <option value="<?php echo $fournisseur['id']; ?>"
                                            <?php echo isset($fournisseur_id) && $fournisseur_id == $fournisseur['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($fournisseur['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>