<?php require_once 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails de la Vente</h1>
        <div>
            <a href="index.php?controller=vente&action=index" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="index.php?controller=vente&action=facture&id=<?php echo $vente['id']; ?>" class="btn btn-primary">
                <i class="fas fa-file-facture"></i> Facture
            </a>
        </div>
    </div>
    
    <?php
    $flash = getFlashMessage();
    if ($flash) {
        echo '<div class="alert alert-' . ($flash['type'] === 'success' ? 'success' : 'danger') . '">' . $flash['message'] . '</div>';
    }
    ?>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations de la Vente</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 40%;">ID de la vente</th>
                                <td><?php echo $vente['id']; ?></td>
                            </tr>
                            <tr>
                                <th>Date de vente</th>
                                <td><?php echo date('d/m/Y H:i', strtotime($vente['date_vente'])); ?></td>
                            </tr>
                            <tr>
                                <th>Statut</th>
                                <td>
                                    <?php if ($vente['statut'] === 'complétée'): ?>
                                        <span class="badge bg-success">Complétée</span>
                                    <?php elseif ($vente['statut'] === 'annulée'): ?>
                                        <span class="badge bg-danger">Annulée</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo $vente['statut']; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Mode de paiement</th>
                                <td><?php echo $vente['mode_paiement']; ?></td>
                            </tr>
                            <tr>
                                <th>Vendeur</th>
                                <td><?php echo $vente['vendeur_nom']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <?php if (isAdmin() && $vente['statut'] !== 'annulée'): ?>
                        <div class="mt-3">
                            <a href="index.php?controller=vente&action=cancel&id=<?php echo $vente['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette vente?');">
                                <i class="fas fa-times"></i> Annuler la vente
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations du Client</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 40%;">Nom</th>
                                <td><?php echo $vente['client_nom']; ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo $vente['client_email']; ?></td>
                            </tr>
                            <tr>
                                <th>Téléphone</th>
                                <td><?php echo $vente['client_telephone']; ?></td>
                            </tr>
                            <tr>
                                <th>Adresse</th>
                                <td><?php echo $vente['client_adresse']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Détails du Produit</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <?php if (!empty($vente['produit_image']) && file_exists($vente['produit_image'])): ?>
                        <img src="<?php echo $vente['produit_image']; ?>" alt="<?php echo $vente['produit_nom']; ?>" class="img-thumbnail" style="max-width: 100%;">
                    <?php else: ?>
                        <img src="assets/img/no-image.png" alt="No Image" class="img-thumbnail" style="max-width: 100%;">
                    <?php endif; ?>
                </div>
                <div class="col-md-10">
                    <h4><?php echo $vente['produit_nom']; ?></h4>
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Prix unitaire</th>
                                <td><?php echo number_format($vente['prix_unitaire'], 2, ',', ' '); ?> DH</td>
                            </tr>
                            <tr>
                                <th>Quantité</th>
                                <td><?php echo $vente['quantite']; ?></td>
                            </tr>
                            <tr>
                                <th>Montant total</th>
                                <td><strong><?php echo number_format($vente['montant_total'], 2, ',', ' '); ?> DH</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>