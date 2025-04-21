<?php require_once 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Catégories</h1>
        <?php if (isAdmin()): ?>
            <a href="index.php?controller=category&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Catégorie
            </a>
        <?php endif; ?>
    </div>
    
    <?php
    $flash = getFlashMessage();
    if ($flash) {
        echo '<div class="alert alert-' . ($flash['type'] === 'success' ? 'success' : 'danger') . '">' . $flash['message'] . '</div>';
    }
    ?>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($categories)): ?>
                <div class="alert alert-info">Aucune catégorie trouvée.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Nombre de Produits</th>
                                <th>Date de Création</th>
                                <?php if (isAdmin()): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><?php echo $category['nom']; ?></td>
                                    <td><?php echo $category['description'] ? $category['description'] : '<em>Aucune description</em>'; ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $category['product_count']; ?></span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($category['date_creation'])); ?></td>
                                    <?php if (isAdmin()): ?>
                                        <td>
                                            <a href="index.php?controller=category&action=edit&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?controller=category&action=delete&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    <?php endif; ?>
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