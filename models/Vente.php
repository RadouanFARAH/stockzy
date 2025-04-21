<?php
class Vente
{
    private $conn;

    // Constructeur
    public function __construct()
    {
        $this->conn = getConnection();
    }

    // Récupérer toutes les ventes avec leurs détails
    public function getAll()
    {
        $stmt = $this->conn->prepare("
        SELECT v.*, 
               p.nom as produit_nom, 
               c.nom as client_nom, 
               u.nom as utilisateur_nom 
        FROM ventes v
        LEFT JOIN produits p ON v.produit_id = p.id
        LEFT JOIN clients c ON v.client_id = c.id
        LEFT JOIN utilisateurs u ON v.vendeur_id = u.id
        ORDER BY v.date_vente DESC
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une vente par son ID
    public function getById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT v.*, 
                   p.nom as produit_nom, 
                   p.prix as produit_prix,
                   p.image as produit_image,
                   c.nom as client_nom, 
                   c.email as client_email,
                   c.telephone as client_telephone,
                   c.adresse as client_adresse,
                   u.nom as vendeur_nom 
            FROM ventes v
            LEFT JOIN produits p ON v.produit_id = p.id
            LEFT JOIN clients c ON v.client_id = c.id
            LEFT JOIN utilisateurs u ON v.vendeur_id = u.id
            WHERE v.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer une nouvelle vente
    public function create($produit_id, $client_id, $vendeur_id, $quantite, $prix_unitaire, $montant_total, $mode_paiement, $statut)
    {
        try {
            // Démarrer une transaction
            $this->conn->beginTransaction();

            // Vérifier le stock disponible
            $stmt = $this->conn->prepare("SELECT quantite_stock FROM produits WHERE id = :produit_id");
            $stmt->bindParam(':produit_id', $produit_id);
            $stmt->execute();
            $produit = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produit || $produit['quantite_stock'] < $quantite) {
                // Annuler la transaction si le stock est insuffisant
                $this->conn->rollBack();
                return false;
            }

            // Insérer la vente
            $stmt = $this->conn->prepare("
                INSERT INTO ventes (produit_id, client_id, vendeur_id, quantite, prix_unitaire, montant_total, mode_paiement, statut, date_vente) 
                VALUES (:produit_id, :client_id, :vendeur_id, :quantite, :prix_unitaire, :montant_total, :mode_paiement, :statut, NOW())
            ");
            $stmt->bindParam(':produit_id', $produit_id);
            $stmt->bindParam(':client_id', $client_id);
            $stmt->bindParam(':vendeur_id', $vendeur_id);
            $stmt->bindParam(':quantite', $quantite);
            $stmt->bindParam(':prix_unitaire', $prix_unitaire);
            $stmt->bindParam(':montant_total', $montant_total);
            $stmt->bindParam(':mode_paiement', $mode_paiement);
            $stmt->bindParam(':statut', $statut);
            $stmt->execute();

            // Mettre à jour le stock du produit
            $stmt = $this->conn->prepare("
                UPDATE produits 
                SET quantite_stock = quantite_stock - :quantite 
                WHERE id = :produit_id
            ");
            $stmt->bindParam(':quantite', $quantite);
            $stmt->bindParam(':produit_id', $produit_id);
            $stmt->execute();

            // Valider la transaction
            $this->conn->commit();

            return true;
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            $this->conn->rollBack();
            return false;
        }
    }

    // Annuler une vente
    public function cancel($id)
    {
        try {
            // Démarrer une transaction
            $this->conn->beginTransaction();

            // Récupérer les informations de la vente
            $stmt = $this->conn->prepare("SELECT produit_id, quantite, statut FROM ventes WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $vente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$vente || $vente['statut'] === 'annulée') {
                // La vente n'existe pas ou est déjà annulée
                $this->conn->rollBack();
                return false;
            }

            // Mettre à jour le statut de la vente
            $stmt = $this->conn->prepare("UPDATE ventes SET statut = 'annulée' WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Remettre la quantité en stock
            $stmt = $this->conn->prepare("
                UPDATE produits 
                SET quantite_stock = quantite_stock + :quantite 
                WHERE id = :produit_id
            ");
            $stmt->bindParam(':quantite', $vente['quantite']);
            $stmt->bindParam(':produit_id', $vente['produit_id']);
            $stmt->execute();

            // Valider la transaction
            $this->conn->commit();

            return true;
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            $this->conn->rollBack();
            return false;
        }
    }

    // Récupérer les ventes par vendeur
    public function getByVendeur($vendeur_id)
    {
        $stmt = $this->conn->prepare("
            SELECT v.*, 
                   p.nom as produit_nom, 
                   c.nom as client_nom
            FROM ventes v
            LEFT JOIN produits p ON v.produit_id = p.id
            LEFT JOIN clients c ON v.client_id = c.id
            WHERE v.vendeur_id = :vendeur_id
            ORDER BY v.date_vente DESC
        ");
        $stmt->bindParam(':vendeur_id', $vendeur_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les ventes par client
    public function getByClient($client_id)
    {
        $stmt = $this->conn->prepare("
            SELECT v.*, 
                   p.nom as produit_nom, 
                   u.nom as vendeur_nom
            FROM ventes v
            LEFT JOIN produits p ON v.produit_id = p.id
            LEFT JOIN utilisateurs u ON v.vendeur_id = u.id
            WHERE v.client_id = :client_id
            ORDER BY v.date_vente DESC
        ");
        $stmt->bindParam(':client_id', $client_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les ventes par produit
    public function getByProduit($produit_id)
    {
        $stmt = $this->conn->prepare("
            SELECT v.*, 
                   c.nom as client_nom, 
                   u.nom as vendeur_nom
            FROM ventes v
            LEFT JOIN clients c ON v.client_id = c.id
            LEFT JOIN utilisateurs u ON v.vendeur_id = u.id
            WHERE v.produit_id = :produit_id
            ORDER BY v.date_vente DESC
        ");
        $stmt->bindParam(':produit_id', $produit_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les ventes par période
    public function getByPeriod($date_debut, $date_fin)
    {
        $stmt = $this->conn->prepare("
            SELECT v.*, 
                   p.nom as produit_nom, 
                   c.nom as client_nom, 
                   u.nom as vendeur_nom
            FROM ventes v
            LEFT JOIN produits p ON v.produit_id = p.id
            LEFT JOIN clients c ON v.client_id = c.id
            LEFT JOIN utilisateurs u ON v.vendeur_id = u.id
            WHERE DATE(v.date_vente) BETWEEN :date_debut AND :date_fin
            ORDER BY v.date_vente DESC
        ");
        $stmt->bindParam(':date_debut', $date_debut);
        $stmt->bindParam(':date_fin', $date_fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les statistiques de ventes par jour
    public function getStatsByDay($nombre_jours = 7)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                DATE(date_vente) as jour,
                COUNT(*) as nombre_ventes,
                SUM(montant_total) as montant_total
            FROM ventes
            WHERE statut != 'annulée'
            AND date_vente >= DATE_SUB(CURRENT_DATE(), INTERVAL :nombre_jours DAY)
            GROUP BY DATE(date_vente)
            ORDER BY jour
        ");
        $stmt->bindParam(':nombre_jours', $nombre_jours, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les statistiques de ventes par mois
    public function getStatsByMonth($nombre_mois = 6)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                YEAR(date_vente) as annee,
                MONTH(date_vente) as mois,
                COUNT(*) as nombre_ventes,
                SUM(montant_total) as montant_total
            FROM ventes
            WHERE statut != 'annulée'
            AND date_vente >= DATE_SUB(CURRENT_DATE(), INTERVAL :nombre_mois MONTH)
            GROUP BY YEAR(date_vente), MONTH(date_vente)
            ORDER BY annee, mois
        ");
        $stmt->bindParam(':nombre_mois', $nombre_mois, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer le total des ventes
    public function getTotalVentes()
    {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as nombre_ventes,
                SUM(montant_total) as montant_total
            FROM ventes
            WHERE statut != 'annulée'
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Rechercher des ventes
    public function search($keyword, $date_debut = null, $date_fin = null, $statut = null)
    {
        $sql = "
            SELECT v.*, 
                   p.nom as produit_nom, 
                   c.nom as client_nom, 
                   u.nom as vendeur_nom
            FROM ventes v
            LEFT JOIN produits p ON v.produit_id = p.id
            LEFT JOIN clients c ON v.client_id = c.id
            LEFT JOIN utilisateurs u ON v.vendeur_id = u.id
            WHERE (p.nom LIKE :keyword OR c.nom LIKE :keyword OR u.nom LIKE :keyword)
        ";

        $params = [':keyword' => '%' . $keyword . '%'];

        if ($date_debut && $date_fin) {
            $sql .= " AND DATE(v.date_vente) BETWEEN :date_debut AND :date_fin";
            $params[':date_debut'] = $date_debut;
            $params[':date_fin'] = $date_fin;
        }

        if ($statut) {
            $sql .= " AND v.statut = :statut";
            $params[':statut'] = $statut;
        }

        $sql .= " ORDER BY v.date_vente DESC";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les ventes récentes
    public function getRecentVentes($limit = 5)
    {
        $stmt = $this->conn->prepare("
        SELECT v.*, 
               p.nom as produit_nom, 
               c.nom as client_nom, 
               u.nom as vendeur_nom 
        FROM ventes v
        LEFT JOIN produits p ON v.produit_id = p.id
        LEFT JOIN clients c ON v.client_id = c.id
        LEFT JOIN utilisateurs u ON v.vendeur_id = u.id
        ORDER BY v.date_vente DESC
        LIMIT :limit
    ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les ventes récentes d'un vendeur
    public function getRecentVentesByVendeur($vendeur_id, $limit = 5)
    {
        $stmt = $this->conn->prepare("
        SELECT v.*, 
               p.nom as produit_nom, 
               c.nom as client_nom
        FROM ventes v
        LEFT JOIN produits p ON v.produit_id = p.id
        LEFT JOIN clients c ON v.client_id = c.id
        WHERE v.vendeur_id = :vendeur_id
        ORDER BY v.date_vente DESC
        LIMIT :limit
    ");
        $stmt->bindParam(':vendeur_id', $vendeur_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les statistiques de ventes par vendeur
    public function getStatsByVendeur($vendeur_id)
    {
        $stmt = $this->conn->prepare("
        SELECT 
            COUNT(*) as nombre_ventes,
            SUM(montant_total) as montant_total,
            COUNT(DISTINCT client_id) as nombre_clients,
            MAX(date_vente) as derniere_vente
        FROM ventes
        WHERE vendeur_id = :vendeur_id
        AND statut = 'complétée'
    ");
        $stmt->bindParam(':vendeur_id', $vendeur_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer les ventes par fournisseur
    public function getVentesByFournisseur($fournisseur_id)
    {
        $stmt = $this->conn->prepare("
        SELECT v.*, 
               p.nom as produit_nom, 
               c.nom as client_nom, 
               u.nom as vendeur_nom
        FROM ventes v
        JOIN produits p ON v.produit_id = p.id
        LEFT JOIN clients c ON v.client_id = c.id
        LEFT JOIN utilisateurs u ON v.vendeur_id = u.id
        WHERE p.fournisseur_id = :fournisseur_id
        AND v.statut = 'complétée'
        ORDER BY v.date_vente DESC
    ");
        $stmt->bindParam(':fournisseur_id', $fournisseur_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère les ventes filtrées et paginées
    public function getFilteredVentes($dateDebut = '', $dateFin = '', $clientId = 0, $produitId = 0, $vendeurId = 0, $statut = '', $limit = 10, $offset = 0)
    {
        $sql = "
        SELECT v.*, 
               p.nom as produit_nom, 
               c.nom as client_nom, 
               u.nom as utilisateur_nom 
        FROM ventes v
        LEFT JOIN produits p ON v.produit_id = p.id
        LEFT JOIN clients c ON v.client_id = c.id
        LEFT JOIN utilisateurs u ON v.vendeur_id = u.id
        WHERE 1=1
    ";

        $params = [];

        if (!empty($dateDebut)) {
            $sql .= " AND DATE(v.date_vente) >= :date_debut";
            $params[':date_debut'] = $dateDebut;
        }

        if (!empty($dateFin)) {
            $sql .= " AND DATE(v.date_vente) <= :date_fin";
            $params[':date_fin'] = $dateFin;
        }

        if ($clientId > 0) {
            $sql .= " AND v.client_id = :client_id";
            $params[':client_id'] = $clientId;
        }

        if ($produitId > 0) {
            $sql .= " AND v.produit_id = :produit_id";
            $params[':produit_id'] = $produitId;
        }

        if ($vendeurId > 0) {
            $sql .= " AND v.vendeur_id = :vendeur_id";
            $params[':vendeur_id'] = $vendeurId;
        }

        if (!empty($statut)) {
            $sql .= " AND v.statut = :statut";
            $params[':statut'] = $statut;
        }

        $sql .= " ORDER BY v.date_vente DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // Compte le nombre total de ventes filtrées

    public function countFilteredVentes($dateDebut = '', $dateFin = '', $clientId = 0, $produitId = 0, $vendeurId = 0, $statut = '')
    {
        $sql = "
        SELECT COUNT(*) as total
        FROM ventes v
        LEFT JOIN produits p ON v.produit_id = p.id
        LEFT JOIN clients c ON v.client_id = c.id
        LEFT JOIN utilisateurs u ON v.vendeur_id = u.id
        WHERE 1=1
    ";

        $params = [];

        if (!empty($dateDebut)) {
            $sql .= " AND DATE(v.date_vente) >= :date_debut";
            $params[':date_debut'] = $dateDebut;
        }

        if (!empty($dateFin)) {
            $sql .= " AND DATE(v.date_vente) <= :date_fin";
            $params[':date_fin'] = $dateFin;
        }

        if ($clientId > 0) {
            $sql .= " AND v.client_id = :client_id";
            $params[':client_id'] = $clientId;
        }

        if ($produitId > 0) {
            $sql .= " AND v.produit_id = :produit_id";
            $params[':produit_id'] = $produitId;
        }

        if ($vendeurId > 0) {
            $sql .= " AND v.vendeur_id = :vendeur_id";
            $params[':vendeur_id'] = $vendeurId;
        }

        if (!empty($statut)) {
            $sql .= " AND v.statut = :statut";
            $params[':statut'] = $statut;
        }

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
