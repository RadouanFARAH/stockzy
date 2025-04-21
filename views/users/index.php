<?php require_once 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Utilisateurs</h1>
        <a href="index.php?controller=user&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvel Utilisateur
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
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['nom']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <?php
                                    switch ($user['role']) {
                                        case 'admin':
                                            echo '<span class="badge bg-danger">Administrateur</span>';
                                            break;
                                        case 'vendeur':
                                            echo '<span class="badge bg-success">Vendeur</span>';
                                            break;
                                        case 'fournisseur':
                                            echo '<span class="badge bg-info">Fournisseur</span>';
                                            break;
                                        default:
                                            echo $user['role'];
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="index.php?controller=user&action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="index.php?controller=user&action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>