<?php
require_once 'models/Client.php';

class ClientController {
    private $clientModel;
    
    public function __construct() {
        $this->clientModel = new Client();
    }
    
    // Afficher la liste des clients
    public function index() {
        // Vérifier si l'utilisateur est connecté
        requireLogin();
        
        // Récupérer tous les clients
        $clients = $this->clientModel->getAll();
        
        // Pour chaque client, compter le nombre de ventes
        foreach ($clients as $key => $client) {
            $clients[$key]['ventes_count'] = $this->clientModel->countVentes($client['id']);
        }
        
        // Afficher la vue
        require_once 'views/clients/index.php';
    }
    
    // Afficher le formulaire de création d'un client
    public function create() {
        // Vérifier si l'utilisateur est administrateur ou vendeur
        if (!isAdmin() && !isVendeur()) {
            requireRole('admin');
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = sanitize($_POST['nom']);
            $adresse = sanitize($_POST['adresse']);
            $telephone = sanitize($_POST['telephone']);
            $email = sanitize($_POST['email']);
            
            // Valider les données
            $errors = [];
            
            if (empty($nom)) {
                $errors[] = "Le nom du client est requis.";
            }
    
            
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            }
            
            // S'il n'y a pas d'erreurs, créer le client
            if (empty($errors)) {
                if ($this->clientModel->create($nom, $adresse, $telephone, $email)) {
                    setFlashMessage('success', 'Client créé avec succès!');
                    redirect('index.php?controller=client&action=index');
                } else {
                    setFlashMessage('error', 'Une erreur est survenue lors de la création du client.');
                }
            } else {
                // Afficher les erreurs
                foreach ($errors as $error) {
                    setFlashMessage('error', $error);
                }
            }
        }
        
        // Afficher la vue
        require_once 'views/clients/create.php';
    }
    
    // Afficher le formulaire de modification d'un client
    public function edit() {
        // Vérifier si l'utilisateur est administrateur ou vendeur
        if (!isAdmin() && !isVendeur()) {
            requireRole('admin');
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de client non spécifié.');
            redirect('index.php?controller=client&action=index');
        }
        
        $id = (int)$_GET['id'];
        
        // Récupérer le client
        $client = $this->clientModel->getById($id);
        
        if (!$client) {
            setFlashMessage('error', 'Client non trouvé.');
            redirect('index.php?controller=client&action=index');
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = sanitize($_POST['nom']);
            $adresse = sanitize($_POST['adresse']);
            $telephone = sanitize($_POST['telephone']);
            $email = sanitize($_POST['email']);
            
            // Valider les données
            $errors = [];
            
            if (empty($nom)) {
                $errors[] = "Le nom du client est requis.";
            }
            
            
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            }
            
            // S'il n'y a pas d'erreurs, mettre à jour le client
            if (empty($errors)) {
                if ($this->clientModel->update($id, $nom, $adresse, $telephone, $email)) {
                    setFlashMessage('success', 'Client mis à jour avec succès!');
                    redirect('index.php?controller=client&action=index');
                } else {
                    setFlashMessage('error', 'Une erreur est survenue lors de la mise à jour du client.');
                }
            } else {
                // Afficher les erreurs
                foreach ($errors as $error) {
                    setFlashMessage('error', $error);
                }
            }
        }
        
        // Afficher la vue
        require_once 'views/clients/edit.php';
    }
    
    // Supprimer un client
    public function delete() {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de client non spécifié.');
            redirect('index.php?controller=client&action=index');
        }
        
        $id = (int)$_GET['id'];
        
        // Supprimer le client
        if ($this->clientModel->delete($id)) {
            setFlashMessage('success', 'Client supprimé avec succès!');
        } else {
            setFlashMessage('error', 'Impossible de supprimer ce client car il a des ventes associées.');
        }
        
        redirect('index.php?controller=client&action=index');
    }
}
?>