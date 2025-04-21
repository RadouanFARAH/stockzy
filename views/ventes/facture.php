<?php
// Désactiver l'affichage des erreurs pour cette page
ini_set('display_errors', 0);

// Définir l'en-tête pour indiquer que c'est un PDF
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #<?php echo $vente['id']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .facture-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .facture-header {
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .facture-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        .facture-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .facture-details-col {
            flex: 1;
        }
        .facture-details-col h3 {
            margin-top: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .facture-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .facture-items th, .facture-items td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .facture-items th {
            background-color: #f5f5f5;
        }
        .facture-total {
            text-align: right;
            margin-top: 20px;
        }
        .facture-total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 5px;
        }
        .facture-total-label {
            width: 150px;
            font-weight: bold;
        }
        .facture-total-value {
            width: 100px;
            text-align: right;
        }
        .facture-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .status-completed {
            color: green;
            font-weight: bold;
        }
        .status-cancelled {
            color: red;
            font-weight: bold;
        }
        .print-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .print-button:hover {
            background-color: #45a049;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                padding: 0;
            }
            .facture-container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print();">Imprimer la facture</button>
    
    <div class="facture-container">
        <div class="facture-header">
            <h1 class="facture-title">FACTURE</h1>
            <p>Facture #<?php echo $vente['id']; ?></p>
            <p>Date: <?php echo date('d/m/Y', strtotime($vente['date_vente'])); ?></p>
            <p>Statut: 
                <?php if ($vente['statut'] === 'complétée'): ?>
                    <span class="status-completed">Payée</span>
                <?php elseif ($vente['statut'] === 'annulée'): ?>
                    <span class="status-cancelled">Annulée</span>
                <?php else: ?>
                    <?php echo $vente['statut']; ?>
                <?php endif; ?>
            </p>
        </div>
        
        <div class="facture-details">
            <div class="facture-details-col">
                <h3>Vendeur</h3>
                <p>Nom: <?php echo $vente['vendeur_nom']; ?></p>
                <p>Entreprise: Stockzy</p>
                <p>Mode de paiement: <?php echo $vente['mode_paiement']; ?></p>
            </div>
            
            <div class="facture-details-col">
                <h3>Client</h3>
                <p>Nom: <?php echo $vente['client_nom']; ?></p>
                <p>Email: <?php echo $vente['client_email']; ?></p>
                <p>Téléphone: <?php echo $vente['client_telephone']; ?></p>
                <p>Adresse: <?php echo $vente['client_adresse']; ?></p>
            </div>
        </div>
        
        <table class="facture-items">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $vente['produit_nom']; ?></td>
                    <td><?php echo number_format($vente['prix_unitaire'], 2, ',', ' '); ?> DH</td>
                    <td><?php echo $vente['quantite']; ?></td>
                    <td><?php echo number_format($vente['montant_total'], 2, ',', ' '); ?> DH</td>
                </tr>
            </tbody>
        </table>
        
        <div class="facture-total">
            <div class="facture-total-row">
                <div class="facture-total-label">Sous-total:</div>
                <div class="facture-total-value"><?php echo number_format($vente['montant_total'], 2, ',', ' '); ?> DH</div>
            </div>
            <div class="facture-total-row">
                <div class="facture-total-label">TVA (20%):</div>
                <div class="facture-total-value"><?php echo number_format($vente['montant_total'] * 0.2, 2, ',', ' '); ?> DH</div>
            </div>
            <div class="facture-total-row">
                <div class="facture-total-label">Total TTC:</div>
                <div class="facture-total-value"><?php echo number_format($vente['montant_total'] * 1.2, 2, ',', ' '); ?> DH</div>
            </div>
        </div>
        
        <div class="facture-footer">
            <p>Merci pour votre achat!</p>
            <p>Pour toute question concernant cette facture, veuillez contacter notre service client.</p>
            <p>Cette facture a été générée automatiquement et ne nécessite pas de signature.</p>
        </div>
    </div>
</body>
</html>