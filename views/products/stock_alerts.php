<?php require_once 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Alertes de Stock</h1>
        <a href="index.php?controller=product&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux produits
        </a>
    </div>
    
    <?php
    $flash = getFlashMessage();
    if ($flash) {
        echo '<div class="alert alert-' . ($flash['type'] === 'success' ? 'success' : 'danger') . '">' . $flash['message'] . '</div>';
    }
    ?>
    
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Produits en alerte de stock</h5>
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Aucun produit n'est en alerte de stock.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Stock</th>
                                <th>Seuil d'alerte</th>
                                <th>Catégorie</th>
                                <th>Fournisseur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['nom']; ?>" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                        <?php else: ?>
                                            <img src="assets/img/no-image.png" alt="No Image" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $product['nom']; ?></td>
                                    <td>
                                        <span class="badge bg-danger"><?php echo $product['quantite_stock']; ?></span>
                                    </td>
                                    <td><?php echo $product['seuil_alerte']; ?></td>
                                    <td><?php echo $product['categorie_nom'] ? $product['categorie_nom'] : '<em>Non catégorisé</em>'; ?></td>
                                    <td><?php echo $product['fournisseur_nom'] ? $product['fournisseur_nom'] : '<em>Non spécifié</em>'; ?></td>
                                    <td>
                                        <a href="index.php?controller=product&action=show&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (isAdmin()): ?>
                                            <a href="index.php?controller=product&action=updateStock&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-boxes"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>