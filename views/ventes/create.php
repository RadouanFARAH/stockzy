<?php require_once 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Nouvelle Vente</h1>
        <a href="index.php?controller=vente&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
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
            <form method="post" action="index.php?controller=vente&action=create" id="venteForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="produit_id" class="form-label">Produit</label>
                            <select class="form-select" id="produit_id" name="produit_id" required>
                                <option value="">Sélectionner un produit</option>
                                <?php foreach ($products as $product): ?>
                                    <?php if ($product['quantite_stock'] > 0): ?>
                                        <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['prix']; ?>" data-stock="<?php echo $product['quantite_stock']; ?>">
                                            <?php echo $product['nom']; ?> - <?php echo number_format($product['prix'], 2, ',', ' '); ?> DH (Stock: <?php echo $product['quantite_stock']; ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Client</label>
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">Sélectionner un client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client['id']; ?>">
                                        <?php echo $client['nom']; ?> (<?php echo $client['email']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantite" class="form-label">Quantité</label>
                            <input type="number" class="form-control" id="quantite" name="quantite" min="1" value="1" required>
                            <div class="form-text" id="stock-info"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mode_paiement" class="form-label">Mode de paiement</label>
                            <select class="form-select" id="mode_paiement" name="mode_paiement" required>
                                <option value="Espèces">Espèces</option>
                                <option value="Carte bancaire">Carte bancaire</option>
                                <option value="Chèque">Chèque</option>
                                <option value="Virement">Virement</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5 class="mb-0">Récapitulatif</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Produit sélectionné</label>
                                    <div id="selected-product" class="form-control-plaintext">-</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Client sélectionné</label>
                                    <div id="selected-client" class="form-control-plaintext">-</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Prix unitaire</label>
                                    <div id="unit-price" class="form-control-plaintext">0,00 DH</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Quantité</label>
                                    <div id="selected-quantity" class="form-control-plaintext">0</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Montant total</label>
                                    <div id="total-amount" class="form-control-plaintext fw-bold fs-4">0,00 DH</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Enregistrer la vente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const produitSelect = document.getElementById('produit_id');
    const clientSelect = document.getElementById('client_id');
    const quantiteInput = document.getElementById('quantite');
    const stockInfo = document.getElementById('stock-info');
    const selectedProduct = document.getElementById('selected-product');
    const selectedClient = document.getElementById('selected-client');
    const unitPrice = document.getElementById('unit-price');
    const selectedQuantity = document.getElementById('selected-quantity');
    const totalAmount = document.getElementById('total-amount');
    
    // Fonction pour mettre à jour le récapitulatif
    function updateSummary() {
        const produitOption = produitSelect.options[produitSelect.selectedIndex];
        const clientOption = clientSelect.options[clientSelect.selectedIndex];
        const quantite = parseInt(quantiteInput.value) || 0;
        
        if (produitSelect.value) {
            const price = parseFloat(produitOption.dataset.price);
            const stock = parseInt(produitOption.dataset.stock);
            
            selectedProduct.textContent = produitOption.text;
            unitPrice.textContent = price.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH';
            
            // Mettre à jour l'information sur le stock
            stockInfo.textContent = `Stock disponible: ${stock} unité(s)`;
            
            // Limiter la quantité au stock disponible
            if (quantite > stock) {
                quantiteInput.value = stock;
                quantite = stock;
            }
            
            // Mettre à jour la quantité et le montant total
            selectedQuantity.textContent = quantite;
            const total = price * quantite;
            totalAmount.textContent = total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH';
        } else {
            selectedProduct.textContent = '-';
            unitPrice.textContent = '0,00 DH';
            stockInfo.textContent = '';
            selectedQuantity.textContent = '0';
            totalAmount.textContent = '0,00 DH';
        }
        
        if (clientSelect.value) {
            selectedClient.textContent = clientOption.text;
        } else {
            selectedClient.textContent = '-';
        }
    }
    
    // Événements pour mettre à jour le récapitulatif
    produitSelect.addEventListener('change', updateSummary);
    clientSelect.addEventListener('change', updateSummary);
    quantiteInput.addEventListener('input', updateSummary);
    
    // Validation du formulaire
    document.getElementById('venteForm').addEventListener('submit', function(event) {
        const produitValue = produitSelect.value;
        const clientValue = clientSelect.value;
        const quantiteValue = quantiteInput.value;
        
        if (!produitValue || !clientValue || !quantiteValue) {
            event.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
        }
    });
    
    // Initialiser le récapitulatif
    updateSummary();
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>