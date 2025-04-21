<?php require_once 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails du Produit</h1>
        <a href="index.php?controller=product&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
    
    <?php
    $flash = getFlashMessage();
    if ($flash) {
        echo '<div class="alert alert-' . ($flash['type'] === 'success' ? 'success' : 'danger') . '">' . $flash['message'] . '</div>';
    }
    ?>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['nom']; ?>" class="img-fluid rounded mb-3" style="max-height: 300px;">
                    <?php else: ?>
                        <img src="assets/img/no-image.png" alt="No Image" class="img-fluid rounded mb-3" style="max-height: 300px;">
                    <?php endif; ?>
                    
                    <h3 class="card-title"><?php echo $product['nom']; ?></h3>
                    <h4 class="text-primary"><?php echo number_format($product['prix'], 2, ',', ' '); ?> DH</h4>
                    
                    <?php if ($product['quantite_stock'] <= $product['seuil_alerte']): ?>
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-exclamation-triangle"></i> Stock bas: <?php echo $product['quantite_stock']; ?> unité(s)
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle"></i> En stock: <?php echo $product['quantite_stock']; ?> unité(s)
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isAdmin()): ?>
                        <div class="mt-3">
                            <a href="index.php?controller=product&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-warning me-2">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="index.php?controller=product&action=updateStock&id=<?php echo $product['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-boxes"></i> Ajuster le stock
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations du Produit</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">ID</th>
                                <td><?php echo $product['id']; ?></td>
                            </tr>
                            <tr>
                                <th>Catégorie</th>
                                <td><?php echo $product['categorie_nom'] ? $product['categorie_nom'] : '<em>Non catégorisé</em>'; ?></td>
                            </tr>
                            <tr>
                                <th>Fournisseur</th>
                                <td><?php echo $product['fournisseur_nom'] ? $product['fournisseur_nom'] : '<em>Non spécifié</em>'; ?></td>
                            </tr>
                            <tr>
                                <th>Seuil d'alerte</th>
                                <td><?php echo $product['seuil_alerte']; ?> unité(s)</td>
                            </tr>
                            <tr>
                                <th>Date de création</th>
                                <td><?php echo date('d/m/Y H:i', strtotime($product['date_creation'])); ?></td>
                            </tr>
                            <tr>
                                <th>Dernière modification</th>
                                <td><?php echo date('d/m/Y H:i', strtotime($product['date_modification'])); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Description</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($product['description'])): ?>
                        <p><?php echo nl2br($product['description']); ?></p>
                    <?php else: ?>
                        <p class="text-muted"><em>Aucune description disponible.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>