<?php require_once 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Clients</h1>
        <?php if (isAdmin() || isVendeur()): ?>
            <a href="index.php?controller=client&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Client
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
            <?php if (empty($clients)): ?>
                <div class="alert alert-info">Aucun client trouvé.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Ventes</th>
                                <?php if (isAdmin() || isVendeur()): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td><?php echo $client['id']; ?></td>
                                    <td><?php echo $client['nom']; ?></td>
                                    <td><?php echo $client['telephone'] ? $client['telephone'] : '<em>Non renseigné</em>'; ?></td>
                                    <td><?php echo $client['email'] ? $client['email'] : '<em>Non renseigné</em>'; ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $client['ventes_count']; ?></span>
                                    </td>
                                    <?php if (isAdmin() || isVendeur()): ?>
                                        <td>
                                            <a href="index.php?controller=client&action=edit&id=<?php echo $client['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (isAdmin()): ?>
                                                <a href="index.php?controller=client&action=delete&id=<?php echo $client['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
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