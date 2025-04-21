<?php
class Product
{
    private $conn;

    // Constructeur
    public function __construct()
    {
        $this->conn = getConnection();
    }

    // Récupérer tous les produits avec leurs catégories et fournisseurs
    public function getAll()
    {
        $stmt = $this->conn->prepare("
        SELECT p.*, c.nom as categorie_nom, f.nom as fournisseur_nom
        FROM produits p
        LEFT JOIN categories c ON p.categorie_id = c.id
        LEFT JOIN utilisateurs f ON p.fournisseur_id = f.id
        ORDER BY p.nom ASC
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un produit par son ID
    public function getById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT p.*, c.nom as categorie_nom, f.nom as fournisseur_nom 
            FROM produits p
            LEFT JOIN categories c ON p.categorie_id = c.id
            LEFT JOIN utilisateurs f ON p.fournisseur_id = f.id
            WHERE p.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Rechercher des produits
    public function search($keyword, $categorie_id = null, $fournisseur_id = null)
    {
        $sql = "
            SELECT p.*, c.nom as categorie_nom, f.nom as fournisseur_nom 
            FROM produits p
            LEFT JOIN categories c ON p.categorie_id = c.id
            LEFT JOIN utilisateurs f ON p.fournisseur_id = f.id
            WHERE p.nom LIKE :keyword OR p.description LIKE :keyword
        ";

        $params = [':keyword' => '%' . $keyword . '%'];

        if ($categorie_id) {
            $sql .= " AND p.categorie_id = :categorie_id";
            $params[':categorie_id'] = $categorie_id;
        }

        if ($fournisseur_id) {
            $sql .= " AND p.fournisseur_id = :fournisseur_id";
            $params[':fournisseur_id'] = $fournisseur_id;
        }

        $sql .= " ORDER BY p.nom";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Créer un nouveau produit
    public function create($nom, $description, $prix, $quantite_stock, $seuil_alerte, $image, $categorie_id, $fournisseur_id)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO produits (nom, description, prix, quantite_stock, seuil_alerte, image, categorie_id, fournisseur_id) 
            VALUES (:nom, :description, :prix, :quantite_stock, :seuil_alerte, :image, :categorie_id, :fournisseur_id)
        ");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':quantite_stock', $quantite_stock);
        $stmt->bindParam(':seuil_alerte', $seuil_alerte);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':categorie_id', $categorie_id);
        $stmt->bindParam(':fournisseur_id', $fournisseur_id);

        return $stmt->execute();
    }

    // Mettre à jour un produit
    public function update($id, $nom, $description, $prix, $quantite_stock, $seuil_alerte, $image, $categorie_id, $fournisseur_id)
    {
        // Si l'image n'est pas modifiée, on ne la met pas à jour
        if (empty($image)) {
            $stmt = $this->conn->prepare("
                UPDATE produits 
                SET nom = :nom, description = :description, prix = :prix, 
                    quantite_stock = :quantite_stock, seuil_alerte = :seuil_alerte, 
                    categorie_id = :categorie_id, fournisseur_id = :fournisseur_id 
                WHERE id = :id
            ");
        } else {
            $stmt = $this->conn->prepare("
                UPDATE produits 
                SET nom = :nom, description = :description, prix = :prix, 
                    quantite_stock = :quantite_stock, seuil_alerte = :seuil_alerte, 
                    image = :image, categorie_id = :categorie_id, fournisseur_id = :fournisseur_id 
                WHERE id = :id
            ");
            $stmt->bindParam(':image', $image);
        }

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':quantite_stock', $quantite_stock);
        $stmt->bindParam(':seuil_alerte', $seuil_alerte);
        $stmt->bindParam(':categorie_id', $categorie_id);
        $stmt->bindParam(':fournisseur_id', $fournisseur_id);

        return $stmt->execute();
    }

    // Mettre à jour le stock d'un produit
    public function updateStock($id, $quantite)
    {
        $stmt = $this->conn->prepare("
            UPDATE produits 
            SET quantite_stock = quantite_stock + :quantite 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':quantite', $quantite);

        return $stmt->execute();
    }

    // Supprimer un produit
    public function delete($id)
    {
        // Vérifier si le produit a des ventes
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM ventes WHERE produit_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return false; // Le produit a des ventes, ne pas supprimer
        }

        // Supprimer le produit
        $stmt = $this->conn->prepare("DELETE FROM produits WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Récupérer les produits en alerte de stock
    public function getStockAlerts()
    {
        $stmt = $this->conn->prepare("
            SELECT p.*, c.nom as categorie_nom, f.nom as fournisseur_nom 
            FROM produits p
            LEFT JOIN categories c ON p.categorie_id = c.id
            LEFT JOIN utilisateurs f ON p.fournisseur_id = f.id
            WHERE p.quantite_stock <= p.seuil_alerte
            ORDER BY p.quantite_stock ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les produits par catégorie
    public function getByCategory($categorie_id)
    {
        $stmt = $this->conn->prepare("
            SELECT p.*, c.nom as categorie_nom, f.nom as fournisseur_nom 
            FROM produits p
            LEFT JOIN categories c ON p.categorie_id = c.id
            LEFT JOIN utilisateurs f ON p.fournisseur_id = f.id
            WHERE p.categorie_id = :categorie_id
            ORDER BY p.nom
        ");
        $stmt->bindParam(':categorie_id', $categorie_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les produits par fournisseur
    public function getByFournisseur($fournisseur_id)
    {
        $stmt = $this->conn->prepare("
            SELECT p.*, c.nom as categorie_nom, f.nom as fournisseur_nom 
            FROM produits p
            LEFT JOIN categories c ON p.categorie_id = c.id
            LEFT JOIN utilisateurs f ON p.fournisseur_id = f.id
            WHERE p.fournisseur_id = :fournisseur_id
            ORDER BY p.nom
        ");
        $stmt->bindParam(':fournisseur_id', $fournisseur_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer le nombre total de produits
    public function getTotalCount()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM produits");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Récupérer les produits les plus vendus
    public function getTopSellingProducts($limit = 5)
    {
        $stmt = $this->conn->prepare("
        SELECT p.*, 
               SUM(v.quantite) as total_vendu,
               COUNT(v.id) as nombre_ventes
        FROM produits p
        JOIN ventes v ON p.id = v.produit_id
        WHERE v.statut = 'complétée'
        GROUP BY p.id
        ORDER BY total_vendu DESC
        LIMIT :limit
    ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Récupérer les produits d'un fournisseur
    public function getProductsByFournisseur($fournisseur_id)
    {
        $stmt = $this->conn->prepare("
        SELECT p.*, 
               c.nom as categorie_nom,
               SUM(v.quantite) as total_vendu
        FROM produits p
        LEFT JOIN categories c ON p.categorie_id = c.id
        LEFT JOIN ventes v ON p.id = v.produit_id AND v.statut = 'complétée'
        WHERE p.fournisseur_id = :fournisseur_id
        GROUP BY p.id
        ORDER BY p.nom
    ");
        $stmt->bindParam(':fournisseur_id', $fournisseur_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère les produits dont le stock est inférieur ou égal au seuil d'alerte

    public function getLowStockProducts()
    {
        $stmt = $this->conn->prepare("
        SELECT p.*, c.nom as categorie_nom, f.nom as fournisseur_nom
        FROM produits p
        LEFT JOIN categories c ON p.categorie_id = c.id
        LEFT JOIN utilisateurs f ON p.fournisseur_id = f.id
        WHERE p.quantite_stock <= p.seuil_alerte
        ORDER BY p.quantite_stock ASC
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Récupère les produits filtrés et paginés
    public function getFilteredProducts($search = '', $categoryId = 0, $fournisseurId = 0, $stockFilter = '', $limit = 10, $offset = 0)
    {
        $sql = "
        SELECT p.*, c.nom as categorie_nom, f.nom as fournisseur_nom
        FROM produits p
        LEFT JOIN categories c ON p.categorie_id = c.id
        LEFT JOIN utilisateurs f ON p.fournisseur_id = f.id
        WHERE 1=1
    ";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND (p.nom LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($categoryId > 0) {
            $sql .= " AND p.categorie_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if ($fournisseurId > 0) {
            $sql .= " AND p.fournisseur_id = :fournisseur_id";
            $params[':fournisseur_id'] = $fournisseurId;
        }

        if ($stockFilter === 'low') {
            $sql .= " AND p.quantite_stock <= p.seuil_alerte AND p.quantite_stock > 0";
        } elseif ($stockFilter === 'out') {
            $sql .= " AND p.quantite_stock <= 0";
        } elseif ($stockFilter === 'available') {
            $sql .= " AND p.quantite_stock > p.seuil_alerte";
        }

        $sql .= " ORDER BY p.nom ASC LIMIT :limit OFFSET :offset";
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

    // Compte le nombre total de produits filtrés
    public function countFilteredProducts($search = '', $categoryId = 0, $fournisseurId = 0, $stockFilter = '')
    {
        $sql = "
        SELECT COUNT(*) as total
        FROM produits p
        LEFT JOIN categories c ON p.categorie_id = c.id
        LEFT JOIN utilisateurs f ON p.fournisseur_id = f.id
        WHERE 1=1
    ";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND (p.nom LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($categoryId > 0) {
            $sql .= " AND p.categorie_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if ($fournisseurId > 0) {
            $sql .= " AND p.fournisseur_id = :fournisseur_id";
            $params[':fournisseur_id'] = $fournisseurId;
        }

        if ($stockFilter === 'low') {
            $sql .= " AND p.quantite_stock <= p.seuil_alerte AND p.quantite_stock > 0";
        } elseif ($stockFilter === 'out') {
            $sql .= " AND p.quantite_stock <= 0";
        } elseif ($stockFilter === 'available') {
            $sql .= " AND p.quantite_stock > p.seuil_alerte";
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
