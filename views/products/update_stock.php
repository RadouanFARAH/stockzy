<?php require_once 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Ajuster le Stock</h1>
        <a href="index.php?controller=product&action=show&id=<?php echo $product['id']; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour au produit
        </a>
    </div>
    
    <?php
    $flash = getFlashMessage();
    if ($flash) {
        echo '<div class="alert alert-' . ($flash['type'] === 'success' ? 'success' : 'danger') . '">' . $flash['message'] . '</div>';
    }
    ?>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations du Produit</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['nom']; ?>" class="img-thumbnail me-3" style="max-width: 80px; max-height: 80px;">
                        <?php else: ?>
                            <img src="assets/img/no-image.png" alt="No Image" class="img-thumbnail me-3" style="max-width: 80px; max-height: 80px;">
                        <?php endif; ?>
                        <div>
                            <h4><?php echo $product['nom']; ?></h4>
                            <p class="text-muted mb-0">ID: <?php echo $product['id']; ?></p>
                        </div>
                    </div>
                    
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Stock actuel</th>
                                <td>
                                    <?php if ($product['quantite_stock'] <= $product['seuil_alerte']): ?>
                                        <span class="badge bg-danger"><?php echo $product['quantite_stock']; ?> unité(s)</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo $product['quantite_stock']; ?> unité(s)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Seuil d'alerte</th>
                                <td><?php echo $product['seuil_alerte']; ?> unité(s)</td>
                            </tr>
                            <tr>
                                <th>Catégorie</th>
                                <td><?php echo $product['categorie_nom'] ? $product['categorie_nom'] : '<em>Non catégorisé</em>'; ?></td>
                            </tr>
                            <tr>
                                <th>Fournisseur</th>
                                <td><?php echo $product['fournisseur_nom'] ? $product['fournisseur_nom'] : '<em>Non spécifié</em>'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ajuster le Stock</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="index.php?controller=product&action=updateStock&id=<?php echo $product['id']; ?>">
                        <div class="mb-3">
                            <label for="quantite" class="form-label">Quantité à ajouter/retirer</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quantite" name="quantite" required>
                                <span class="input-group-text">unité(s)</span>
                            </div>
                            <div class="form-text">
                                Entrez un nombre positif pour ajouter au stock, ou un nombre négatif pour retirer du stock.
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Stock actuel: <strong><?php echo $product['quantite_stock']; ?></strong> unité(s)
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Mettre à jour le stock</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>