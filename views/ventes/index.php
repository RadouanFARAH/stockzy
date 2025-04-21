<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Ventes</h1>
        <?php if (isAdmin() || isVendeur()): ?>
        <a href="index.php?controller=vente&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Vente
        </a>
        <?php endif; ?>
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
                <input type="hidden" name="controller" value="vente">
                <input type="hidden" name="action" value="index">
                
                <div class="col-md-3">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" class="form-control" id="date_debut" name="date_debut" value="<?php echo isset($_GET['date_debut']) ? htmlspecialchars($_GET['date_debut']) : ''; ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="date" class="form-control" id="date_fin" name="date_fin" value="<?php echo isset($_GET['date_fin']) ? htmlspecialchars($_GET['date_fin']) : ''; ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="client" class="form-label">Client</label>
                    <select class="form-select" id="client" name="client">
                        <option value="">Tous les clients</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['id']; ?>" <?php echo (isset($_GET['client']) && $_GET['client'] == $client['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($client['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="produit" class="form-label">Produit</label>
                    <select class="form-select" id="produit" name="produit">
                        <option value="">Tous les produits</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>" <?php echo (isset($_GET['produit']) && $_GET['produit'] == $product['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($product['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (isAdmin()): ?>
                <div class="col-md-3">
                    <label for="utilisateur" class="form-label">Vendeur</label>
                    <select class="form-select" id="utilisateur" name="utilisateur">
                        <option value="">Tous les vendeurs</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo (isset($_GET['utilisateur']) && $_GET['utilisateur'] == $user['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="col-md-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select class="form-select" id="statut" name="statut">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" <?php echo (isset($_GET['statut']) && $_GET['statut'] == 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                        <option value="complétée" <?php echo (isset($_GET['statut']) && $_GET['statut'] == 'complétée') ? 'selected' : ''; ?>>Complétée</option>
                        <option value="annulée" <?php echo (isset($_GET['statut']) && $_GET['statut'] == 'annulée') ? 'selected' : ''; ?>>Annulée</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="index.php?controller=vente&action=index" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des ventes -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Ventes</h5>
            <span class="badge bg-primary"><?php echo count($ventes); ?> ventes</span>
        </div>
        <div class="card-body">
            <?php if (empty($ventes)): ?>
                <div class="alert alert-info">
                    Aucune vente trouvée.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Produit</th>
                                <th>Client</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Montant total</th>
                                <?php if (isAdmin()): ?>
                                    <th>Vendeur</th>
                                <?php endif; ?>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ventes as $vente): ?>
                                <tr>
                                    <td><?php echo $vente['id']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($vente['date_vente'])); ?></td>
                                    <td><?php echo htmlspecialchars($vente['produit_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($vente['client_nom']); ?></td>
                                    <td><?php echo $vente['quantite']; ?></td>
                                    <td><?php echo number_format($vente['prix_unitaire'], 2, ',', ' '); ?> DH</td>
                                    <td><?php echo number_format($vente['montant_total'], 2, ',', ' '); ?> DH</td>
                                    <?php if (isAdmin()): ?>
                                        <td><?php echo htmlspecialchars($vente['utilisateur_nom']); ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <?php if ($vente['statut'] === 'complétée'): ?>
                                            <span class="badge bg-success">Complétée</span>
                                        <?php elseif ($vente['statut'] === 'en_attente'): ?>
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Annulée</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?controller=vente&action=show&id=<?php echo $vente['id']; ?>" class="btn btn-info" title="Détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?controller=vente&action=facture&id=<?php echo $vente['id']; ?>" class="btn btn-secondary" title="Facture">
                                                <i class="fas fa-file-facture"></i>
                                            </a>
                                            <?php if (($vente['statut'] !== 'annulée') && (isAdmin() || (isVendeur() && $vente['vendeur_id'] == $_SESSION['user_id']))): ?>
                                                <a href="index.php?controller=vente&action=cancel&id=<?php echo $vente['id']; ?>" class="btn btn-danger" title="Annuler" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette vente ?');">
                                                    <i class="fas fa-times"></i>
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
                                <a class="page-link" href="index.php?controller=vente&action=index&page=<?php echo $currentPage - 1; ?><?php echo $queryString; ?>" aria-label="Précédent">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($currentPage == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="index.php?controller=vente&action=index&page=<?php echo $i; ?><?php echo $queryString; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="index.php?controller=vente&action=index&page=<?php echo $currentPage + 1; ?><?php echo $queryString; ?>" aria-label="Suivant">
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