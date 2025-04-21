<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Produits</h1>
        <a href="index.php?controller=product&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Produit
        </a>
    </div>

    <?php
    $flash = getFlashMessage();
    if ($flash) {
        echo '<div class="alert alert-' . ($flash['type'] === 'success' ? 'success' : 'danger') . ' alert-dismissible fade show">' . $flash['message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    ?>

    <!-- Filtres de recherche -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtres de recherche</h5>
        </div>
        <div class="card-body">
            <form action="index.php" method="get" class="row g-3">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="index">
                
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Nom ou description" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="category" class="form-label">Catégorie</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (isAdmin()): ?>
                <div class="col-md-3">
                    <label for="fournisseur" class="form-label">Fournisseur</label>
                    <select class="form-select" id="fournisseur" name="fournisseur">
                        <option value="">Tous les fournisseurs</option>
                        <?php foreach ($fournisseurs as $fournisseur): ?>
                            <option value="<?php echo $fournisseur['id']; ?>" <?php echo (isset($_GET['fournisseur']) && $_GET['fournisseur'] == $fournisseur['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($fournisseur['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="col-md-3">
                    <label for="stock" class="form-label">Stock</label>
                    <select class="form-select" id="stock" name="stock">
                        <option value="">Tous les niveaux</option>
                        <option value="low" <?php echo (isset($_GET['stock']) && $_GET['stock'] == 'low') ? 'selected' : ''; ?>>Stock faible</option>
                        <option value="out" <?php echo (isset($_GET['stock']) && $_GET['stock'] == 'out') ? 'selected' : ''; ?>>Rupture de stock</option>
                        <option value="available" <?php echo (isset($_GET['stock']) && $_GET['stock'] == 'available') ? 'selected' : ''; ?>>Disponible</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="index.php?controller=product&action=index" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des produits -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Produits</h5>
            <span class="badge bg-primary"><?php echo count($products); ?> produits</span>
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    Aucun produit trouvé.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Stock</th>
                                <th>Fournisseur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>" class="img-thumbnail" style="max-width: 50px;">
                                        <?php else: ?>
                                            <div class="bg-light text-center" style="width: 50px; height: 50px; line-height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($product['categorie_nom'] ?? 'Non catégorisé'); ?></td>
                                    <td><?php echo number_format($product['prix'], 2, ',', ' '); ?> DH</td>
                                    <td>
                                        <?php if ($product['quantite_stock'] <= 0): ?>
                                            <span class="badge bg-danger">Rupture</span>
                                        <?php elseif ($product['quantite_stock'] <= $product['seuil_alerte']): ?>
                                            <span class="badge bg-warning text-dark"><?php echo $product['quantite_stock']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $product['quantite_stock']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['fournisseur_nom'] ?? 'Non spécifié'); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?controller=product&action=show&id=<?php echo $product['id']; ?>" class="btn btn-info" title="Détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (isAdmin() || (isFournisseur() && $product['fournisseur_id'] == $_SESSION['user_id'])): ?>
                                                <a href="index.php?controller=product&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="index.php?controller=product&action=updateStock&id=<?php echo $product['id']; ?>" class="btn btn-warning" title="Mettre à jour le stock">
                                                    <i class="fas fa-boxes"></i>
                                                </a>
                                                <a href="index.php?controller=product&action=delete&id=<?php echo $product['id']; ?>" class="btn btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (isVendeur()): ?>
                                                <a href="index.php?controller=vente&action=create&product_id=<?php echo $product['id']; ?>" class="btn btn-success" title="Vendre">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="index.php?controller=product&action=index&page=<?php echo $currentPage - 1; ?><?php echo $queryString; ?>" aria-label="Précédent">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($currentPage == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="index.php?controller=product&action=index&page=<?php echo $i; ?><?php echo $queryString; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="index.php?controller=product&action=index&page=<?php echo $currentPage + 1; ?><?php echo $queryString; ?>" aria-label="Suivant">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>