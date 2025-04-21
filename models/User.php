<?php
class User
{
    private $conn;

    // Constructeur
    public function __construct()
    {
        $this->conn = getConnection();
    }

    // Récupérer tous les utilisateurs
    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT id, nom, email, role FROM utilisateurs");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les fournisseurs
    public function getAllFournisseurs()
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM utilisateurs 
            WHERE role = 'fournisseur'
            ORDER BY nom ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer le nombre total de fournisseurs
    public function getTotalCountFournisseurs()
    {
        $stmt = $this->conn->prepare("
        SELECT COUNT(*) as total FROM utilisateurs
        WHERE role = 'fournisseur'
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Compter le nombre de produits d'un utilisateur
    public function countProductsByUser($id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM produits WHERE fournisseur_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Récupérer un utilisateur par son ID
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT id, nom, email, role FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Récupérer un utilisateur par son email
    public function getByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouvel utilisateur
    public function create($nom, $email, $mot_de_passe, $role)
    {
        $hashed_password = hashPassword($mot_de_passe);

        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (:nom, :email, :mot_de_passe, :role)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $hashed_password);
        $stmt->bindParam(':role', $role);

        return $stmt->execute();
    }

    // Mettre à jour un utilisateur
    public function update($id, $nom, $email, $role)
    {
        $stmt = $this->conn->prepare("UPDATE utilisateurs SET nom = :nom, email = :email, role = :role WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);

        return $stmt->execute();
    }

    // Mettre à jour le mot de passe d'un utilisateur
    public function updatePassword($id, $mot_de_passe)
    {
        $hashed_password = hashPassword($mot_de_passe);

        $stmt = $this->conn->prepare("UPDATE utilisateurs SET mot_de_passe = :mot_de_passe WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':mot_de_passe', $hashed_password);

        return $stmt->execute();
    }

    // Supprimer un utilisateur
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Récupérer le nombre total d'utilisateurs
    public function getTotalCount()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM utilisateurs");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Récupérer les statistiques des utilisateurs par rôle
    public function getUserStats()
    {
        $stmt = $this->conn->prepare("
        SELECT 
            role,
            COUNT(*) as nombre
        FROM utilisateurs
        GROUP BY role
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère tous les utilisateurs par rôle
    public function getAllByRole($role)
    {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE role = :role ORDER BY nom ASC");
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
