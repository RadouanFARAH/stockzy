<?php
class Category
{
    private $conn;

    // Constructeur
    public function __construct()
    {
        $this->conn = getConnection();
    }

    // Récupérer toutes les catégories
    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une catégorie par son ID
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Vérifier si une catégorie existe avec ce nom
    public function existsByName($nom, $excludeId = null)
    {
        if ($excludeId) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM categories WHERE nom = :nom AND id != :id");
            $stmt->bindParam(':id', $excludeId);
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM categories WHERE nom = :nom");
        }
        $stmt->bindParam(':nom', $nom);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Créer une nouvelle catégorie
    public function create($nom, $description)
    {
        $stmt = $this->conn->prepare("INSERT INTO categories (nom, description) VALUES (:nom, :description)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    // Mettre à jour une catégorie
    public function update($id, $nom, $description)
    {
        $stmt = $this->conn->prepare("UPDATE categories SET nom = :nom, description = :description WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    // Supprimer une catégorie
    public function delete($id)
    {
        // Vérifier si la catégorie est utilisée par des produits
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM produits WHERE categorie_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return false; // La catégorie est utilisée, ne pas supprimer
        }

        // Supprimer la catégorie
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Compter le nombre de produits dans une catégorie
    public function countProducts($id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM produits WHERE categorie_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Récupérer le nombre total de catégories
    public function getTotalCount()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM categories");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
