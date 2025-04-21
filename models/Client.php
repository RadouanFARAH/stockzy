<?php
class Client
{
    private $conn;

    // Constructeur
    public function __construct()
    {
        $this->conn = getConnection();
    }

    // Récupérer tous les clients
    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT * FROM clients ORDER BY nom");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un client par son ID
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM clients WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouveau client
    public function create($nom, $adresse, $telephone, $email)
    {
        $stmt = $this->conn->prepare("INSERT INTO clients (nom, adresse, telephone, email) VALUES (:nom, :adresse, :telephone, :email)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    // Mettre à jour un client
    public function update($id, $nom, $adresse, $telephone, $email)
    {
        $stmt = $this->conn->prepare("UPDATE clients SET nom = :nom, adresse = :adresse, telephone = :telephone, email = :email WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    // Supprimer un client
    public function delete($id)
    {
        // Vérifier si le client a des ventes
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM ventes WHERE client_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return false; // Le client a des ventes, ne pas supprimer
        }

        // Supprimer le client
        $stmt = $this->conn->prepare("DELETE FROM clients WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Compter le nombre de ventes d'un client
    public function countVentes($id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM ventes WHERE client_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Récupérer le nombre total de clients
    public function getTotalCount()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM clients");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Récupérer les clients d'un vendeur
    public function getClientsByVendeur($vendeur_id)
    {
        $stmt = $this->conn->prepare("
        SELECT c.*, 
               COUNT(v.id) as nombre_achats,
               SUM(v.montant_total) as montant_total
        FROM clients c
        JOIN ventes v ON c.id = v.client_id
        WHERE v.vendeur_id = :vendeur_id
        AND v.statut = 'complétée'
        GROUP BY c.id
        ORDER BY nombre_achats DESC
    ");
        $stmt->bindParam(':vendeur_id', $vendeur_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère les clients d'un utilisateur
    public function getClientsByUtilisateur($vendeur_id)
    {
        $stmt = $this->conn->prepare("
        SELECT c.*, 
               COUNT(v.id) as nombre_achats,
               SUM(v.montant_total) as montant_total
        FROM clients c
        JOIN ventes v ON c.id = v.client_id
        WHERE v.vendeur_id = :vendeur_id
        GROUP BY c.id
        ORDER BY nombre_achats DESC
    ");
        $stmt->bindParam(':vendeur_id', $vendeur_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
