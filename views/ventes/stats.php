<?php require_once 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Statistiques des Ventes</h1>
        <a href="index.php?controller=vente&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux ventes
        </a>
    </div>

    <?php
    $flash = getFlashMessage();
    if ($flash) {
        echo '<div class="alert alert-' . ($flash['type'] === 'success' ? 'success' : 'danger') . '">' . $flash['message'] . '</div>';
    }
    ?>

    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Total des Ventes</h5>
                    <h2 class="display-4">
                        <?php echo number_format($totalVentes['montant_total'], 2, ',', ' '); ?> DH
                    </h2>
                    <p class="text-muted">
                        <?php echo $totalVentes['nombre_ventes']; ?> ventes au total
                    </p>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Ventes des 30 derniers jours</h5>
                </div>
                <div class="card-body">
                    <canvas id="ventesByDayChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Ventes mensuelles</h5>
                </div>
                <div class="card-body">
                    <canvas id="ventesByMonthChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclure Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Données pour le graphique des ventes par jour
            const dayLabels = <?php
                                $labels = [];
                                $amounts = [];
                                $counts = [];

                                foreach ($statsByDay as $stat) {
                                    $labels[] = date('d/m', strtotime($stat['jour']));
                                    $amounts[] = $stat['montant_total'];
                                    $counts[] = $stat['nombre_ventes'];
                                }

                                echo json_encode($labels);
                                ?>;

            const dayAmounts = <?php echo json_encode($amounts); ?>;
            const dayCounts = <?php echo json_encode($counts); ?>;

            // Créer le graphique des ventes par jour
            const ctxDay = document.getElementById('ventesByDayChart').getContext('2d');
            new Chart(ctxDay, {
                type: 'line',
                data: {
                    labels: dayLabels,
                    datasets: [{
                            label: 'Montant des ventes (DH)',
                            data: dayAmounts,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Nombre de ventes',
                            data: dayCounts,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Montant (DH)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: 'Nombre de ventes'
                            }
                        }
                    }
                }
            });

            // Données pour le graphique des ventes par mois
            const monthLabels = <?php
                                $labels = [];
                                $amounts = [];
                                $counts = [];

                                foreach ($statsByMonth as $stat) {
                                    $monthName = date('F Y', mktime(0, 0, 0, $stat['mois'], 1, $stat['annee']));
                                    $labels[] = $monthName;
                                    $amounts[] = $stat['montant_total'];
                                    $counts[] = $stat['nombre_ventes'];
                                }

                                echo json_encode($labels);
                                ?>;

            const monthAmounts = <?php echo json_encode($amounts); ?>;
            const monthCounts = <?php echo json_encode($counts); ?>;

            // Créer le graphique des ventes par mois
            const ctxMonth = document.getElementById('ventesByMonthChart').getContext('2d');
            new Chart(ctxMonth, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                            label: 'Montant des ventes (DH)',
                            data: monthAmounts,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Nombre de ventes',
                            data: monthCounts,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Montant (DH)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: 'Nombre de ventes'
                            }
                        }
                    }
                }
            });
        });
    </script>

    <?php require_once 'views/layouts/footer.php'; ?>