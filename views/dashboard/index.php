<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid">
    <h1 class="mt-4 mb-4">Tableau de bord</h1>

    <?php
    $flash = getFlashMessage();
    if ($flash) {
        echo '<div class="alert alert-' . ($flash['type'] === 'success' ? 'success' : 'danger') . ' alert-dismissible fade show">' . $flash['message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    ?>

    <!-- Cartes de statistiques principales -->
    <div class="row">
    <?php if (isAdmin()): ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Ventes totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($totalVentes['montant_total'] ?? 0, 2, ',', ' '); ?> DH
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <?php echo $totalVentes['nombre_ventes'] ?? 0; ?> ventes
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isAdmin()): ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Produits en stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $totalProducts; ?>
                            </div>
                            <div class="text-xs text-danger mt-1">
                                <?php echo count($lowStockProducts); ?> produits en stock faible
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isAdmin()): ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Clients</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $totalClients; ?>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                Base de clients
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

        <?php if (isAdmin()): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Utilisateurs</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $totalUsers; ?>
                                </div>
                                <div class="text-xs text-muted mt-1">
                                    Équipe de gestion
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif (isVendeur()): ?>
            <div class="col-xl-3 col-md-6 mb-4 mx-auto">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Mes ventes</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($vendeurVentes['montant_total'] ?? 0, 2, ',', ' '); ?> DH
                                </div>
                                <div class="text-xs text-muted mt-1">
                                    <?php echo $vendeurVentes['nombre_ventes'] ?? 0; ?> ventes réalisées
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif (isFournisseur()): ?>
            <div class="col-xl-3 col-md-6 mb-4 mx-auto">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Mes produits</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo count($fournisseurProducts); ?>
                                </div>
                                <div class="text-xs text-muted mt-1">
                                    Produits fournis
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Graphiques et tableaux -->
    <?php if (isAdmin()): ?>
    <div class="row d-flex align-items-stretch">
        <!-- Graphique des ventes -->
        <div class="col-xl-6 col-lg-6 d-flex">
            <div class="card shadow mb-4 w-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution des ventes</h6>
                    <div class="dropdown">
                        <a href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#" id="view-daily">Vue journalière</a>
                            <a class="dropdown-item" href="#" id="view-monthly">Vue mensuelle</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="index.php?controller=vente&action=stats">Voir toutes les statistiques</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="ventesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits en stock faible -->
        <div class="col-xl-6 col-lg-6 d-flex">
            <div class="card shadow mb-4 w-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger">Alertes de stock</h6>
                    <a href="index.php?controller=product&action=stockAlerts" class="btn btn-sm btn-danger">
                        Voir tout
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($lowStockProducts)): ?>
                        <div class="text-center">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p>Tous les produits ont un niveau de stock suffisant.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Stock</th>
                                        <th>Seuil</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($lowStockProducts, 0, 5) as $product): ?>
                                        <tr>
                                            <td><?php echo $product['nom']; ?></td>
                                            <td>
                                                <span class="badge bg-danger"><?php echo $product['quantite_stock']; ?></span>
                                            </td>
                                            <td><?php echo $product['seuil_alerte']; ?></td>
                                            <td>
                                                <?php if (isAdmin()): ?>

                                                    <a href="index.php?controller=product&action=updateStock&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (count($lowStockProducts) > 5): ?>
                                <div class="text-center mt-2">
                                    <a href="index.php?controller=product&action=stockAlerts" class="btn btn-sm btn-outline-danger">
                                        Voir les <?php echo count($lowStockProducts) - 5; ?> autres produits
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex align-items-stretch">
        <!-- Produits les plus vendus -->
        <div class="col-xl-6 col-lg-6 mb-4 d-flex">
            <div class="card shadow mb-4 w-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produits les plus vendus</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($topProducts)): ?>
                        <div class="text-center">
                            <p>Aucune donnée de vente disponible.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Catégorie</th>
                                        <th>Prix</th>
                                        <th>Quantité vendue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topProducts as $product): ?>
                                        <tr>
                                            <td>
                                                <a href="index.php?controller=product&action=show&id=<?php echo $product['id']; ?>">
                                                    <?php echo $product['nom']; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $product['categorie_nom'] ?? 'Non catégorisé'; ?></td>
                                            <td><?php echo number_format($product['prix'], 2, ',', ' '); ?> DH</td>
                                            <td>
                                                <span class="badge bg-success"><?php echo $product['total_vendu']; ?></span>
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

        <!-- Dernières ventes -->
        <div class="col-xl-6 col-lg-6 mb-4 d-flex">
            <div class="card shadow mb-4 w-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Dernières ventes</h6>
                    <a href="index.php?controller=vente&action=index" class="btn btn-sm btn-primary">
                        Voir toutes les ventes
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentVentes)): ?>
                        <div class="text-center">
                            <p>Aucune vente récente.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Produit</th>
                                        <th>Client</th>
                                        <th>Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentVentes as $vente): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y H:i', strtotime($vente['date_vente'])); ?></td>
                                            <td><?php echo $vente['produit_nom']; ?></td>
                                            <td><?php echo $vente['client_nom']; ?></td>
                                            <td><?php echo number_format($vente['montant_total'], 2, ',', ' '); ?> DH</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if (isAdmin()): ?>
        <!-- Section spécifique à l'administrateur -->
        <div class="row d-flex align-items-stretch">
            <!-- Statistiques générales -->
            <div class="col-lg-12 mb-4 d-flex">
                <div class="card shadow mb-4 w-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistiques générales</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Catégories</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalCategories; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-tags fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Fournisseurs</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalFournisseurs; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-truck fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="text-center">
                                    <a href="index.php?controller=user&action=index" class="btn btn-sm btn-primary mr-2">
                                        <i class="fas fa-users"></i> Gérer les utilisateurs
                                    </a>
                                    <a href="index.php?controller=category&action=index" class="btn btn-sm btn-success mr-2">
                                        <i class="fas fa-tags"></i> Gérer les catégories
                                    </a>
                                    <a href="index.php?controller=client&action=index" class="btn btn-sm btn-success mr-2">
                                        <i class="fas fa-users me-1"></i> Gérer les Clients
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isVendeur()): ?>
        <!-- Section spécifique au vendeur -->
        <div class="row">
            <!-- Mes clients -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Mes clients</h6>
                        <a href="index.php?controller=client&action=index" class="btn btn-sm btn-primary">
                            Voir tous les clients
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($vendeurClients)): ?>
                            <div class="text-center">
                                <p>Vous n'avez pas encore de clients.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Achats</th>
                                            <th>Montant total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($vendeurClients, 0, 5) as $client): ?>
                                            <tr>
                                                <td><?php echo $client['nom']; ?></td>
                                                <td><?php echo $client['email']; ?></td>
                                                <td><?php echo $client['telephone']; ?></td>
                                                <td><?php echo $client['nombre_achats']; ?></td>
                                                <td><?php echo number_format($client['montant_total'], 2, ',', ' '); ?> DH</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($vendeurClients) > 5): ?>
                                    <div class="text-center mt-2">
                                        <a href="index.php?controller=client&action=index" class="btn btn-sm btn-outline-primary">
                                            Voir les <?php echo count($vendeurClients) - 5; ?> autres clients
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isFournisseur()): ?>
        <!-- Section spécifique au fournisseur -->
        <div class="row">
            <!-- Mes produits -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Mes produits</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($fournisseurProducts)): ?>
                            <div class="text-center">
                                <p>Vous n'avez pas encore de produits enregistrés.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Produit</th>
                                            <th>Catégorie</th>
                                            <th>Prix</th>
                                            <th>Stock</th>
                                            <th>Ventes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($fournisseurProducts, 0, 5) as $product): ?>
                                            <tr>
                                                <td><?php echo $product['nom']; ?></td>
                                                <td><?php echo $product['categorie_nom'] ?? 'Non catégorisé'; ?></td>
                                                <td><?php echo number_format($product['prix'], 2, ',', ' '); ?> DH</td>
                                                <td>
                                                    <?php if ($product['quantite_stock'] <= $product['seuil_alerte']): ?>
                                                        <span class="badge bg-danger"><?php echo $product['quantite_stock']; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success"><?php echo $product['quantite_stock']; ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $product['total_vendu'] ?? 0; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($fournisseurProducts) > 5): ?>
                                    <div class="text-center mt-2">
                                        <a href="index.php?controller=product&action=index" class="btn btn-sm btn-outline-primary">
                                            Voir les <?php echo count($fournisseurProducts) - 5; ?> autres produits
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Scripts pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour le graphique des ventes
        const ventesData = {
            daily: {
                labels: <?php
                        $labels = [];
                        $amounts = [];

                        foreach ($ventesByDay as $stat) {
                            $labels[] = date('d/m', strtotime($stat['jour']));
                            $amounts[] = $stat['montant_total'];
                        }

                        echo json_encode($labels);
                        ?>,
                data: <?php echo json_encode($amounts); ?>
            },
            monthly: {
                labels: <?php
                        $labels = [];
                        $amounts = [];

                        foreach ($ventesByMonth as $stat) {
                            $monthName = date('M Y', mktime(0, 0, 0, $stat['mois'], 1, $stat['annee']));
                            $labels[] = $monthName;
                            $amounts[] = $stat['montant_total'];
                        }

                        echo json_encode($labels);
                        ?>,
                data: <?php echo json_encode($amounts); ?>
            }
        };

        // Configuration du graphique des ventes
        let currentView = 'daily';
        const ctxVentes = document.getElementById('ventesChart').getContext('2d');
        const ventesChart = new Chart(ctxVentes, {
            type: 'line',
            data: {
                labels: ventesData[currentView].labels,
                datasets: [{
                    label: 'Montant des ventes (DH)',
                    data: ventesData[currentView].data,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value, index, values) {
                                return value.toLocaleString('fr-FR') + ' DH';
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('fr-FR', {
                                        style: 'currency',
                                        currency: 'MAD',
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Changer la vue du graphique
        document.getElementById('view-daily').addEventListener('click', function(e) {
            e.preventDefault();
            currentView = 'daily';
            ventesChart.data.labels = ventesData[currentView].labels;
            ventesChart.data.datasets[0].data = ventesData[currentView].data;
            ventesChart.update();
        });

        document.getElementById('view-monthly').addEventListener('click', function(e) {
            e.preventDefault();
            currentView = 'monthly';
            ventesChart.data.labels = ventesData[currentView].labels;
            ventesChart.data.datasets[0].data = ventesData[currentView].data;
            ventesChart.update();
        });
    });
</script>

<?php require_once 'views/layouts/footer.php'; ?>