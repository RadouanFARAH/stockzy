<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stockzy</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-store me-2"></i>
                Stockzy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isLoggedIn()): ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($controller === 'dashboard') ? 'active' : ''; ?>" href="index.php">
                                <i class="fas fa-tachometer-alt me-1"></i> Tableau de bord
                            </a>
                        </li>

                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($controller === 'product') ? 'active' : ''; ?>" href="index.php?controller=product&action=index">
                                    <i class="fas fa-box me-1"></i> Produits
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($controller === 'vente') ? 'active' : ''; ?>" href="index.php?controller=vente&action=index">
                                    <i class="fas fa-shopping-cart me-1"></i> Ventes
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (isVendeur()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($controller === 'vente') ? 'active' : ''; ?>" href="index.php?controller=vente&action=index">
                                    <i class="fas fa-shopping-cart me-1"></i>Mes Ventes
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (isFournisseur()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($controller === 'product') ? 'active' : ''; ?>" href="index.php?controller=product&action=index">
                                    <i class="fas fa-boxes me-1"></i> Mes Produits
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <ul class="navbar-nav">
                        <?php if (isAdmin()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-cogs me-1"></i> Administration
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li>
                                        <a class="dropdown-item" href="index.php?controller=user&action=index">
                                            <i class="fas fa-user-shield me-1"></i> Utilisateurs
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="index.php?controller=category&action=index">
                                            <i class="fas fa-tags me-1"></i> Catégories
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="index.php?controller=client&action=index">
                                            <i class="fas fa-users me-1"></i> Clients
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=auth&action=logout">
                                        <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">