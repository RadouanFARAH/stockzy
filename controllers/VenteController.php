<?php
require_once 'models/Vente.php';
require_once 'models/Product.php';
require_once 'models/Client.php';
require_once 'models/User.php';

class VenteController {
    private $venteModel;
    private $productModel;
    private $clientModel;
    private $userModel;
    
    public function __construct() {
        $this->venteModel = new Vente();
        $this->productModel = new Product();
        $this->clientModel = new Client();
        $this->userModel = new User();
    }
    
    // Afficher la liste des ventes
    public function index() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            // Rediriger vers la page de connexion
            if (isset($_SESSION)) {
                $_SESSION['flash_message'] = [
                    'message' => 'Vous devez être connecté pour accéder à cette page.',
                    'type' => 'danger'
                ];
            }
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer les paramètres de filtrage
        $dateDebut = isset($_GET['date_debut']) ? sanitize($_GET['date_debut']) : '';
        $dateFin = isset($_GET['date_fin']) ? sanitize($_GET['date_fin']) : '';
        $clientId = isset($_GET['client']) ? intval($_GET['client']) : 0;
        $produitId = isset($_GET['produit']) ? intval($_GET['produit']) : 0;
        $vendeurId = isset($_GET['utilisateur']) ? intval($_GET['utilisateur']) : 0;
        $statut = isset($_GET['statut']) ? sanitize($_GET['statut']) : '';
        
        // Si l'utilisateur n'est pas admin, limiter aux ventes de l'utilisateur connecté
        if (!isAdmin()) {
            $vendeurId = $_SESSION['user_id'];
        }
        
        // Pagination
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Construire la chaîne de requête pour la pagination
        $queryParams = [];
        if (!empty($dateDebut)) $queryParams[] = 'date_debut=' . urlencode($dateDebut);
        if (!empty($dateFin)) $queryParams[] = 'date_fin=' . urlencode($dateFin);
        if ($clientId > 0) $queryParams[] = 'client=' . $clientId;
        if ($produitId > 0) $queryParams[] = 'produit=' . $produitId;
        if ($vendeurId > 0 && isAdmin()) $queryParams[] = 'utilisateur=' . $vendeurId;
        if (!empty($statut)) $queryParams[] = 'statut=' . urlencode($statut);
        $queryString = !empty($queryParams) ? '&' . implode('&', $queryParams) : '';
        
        if (isVendeur()) {
            // Si l'utilisateur est un vendeur, filtrer par son ID
            $vendeurId = $_SESSION['user_id'];
        }
        // Récupérer les ventes filtrées et paginées
        $ventes = $this->venteModel->getFilteredVentes($dateDebut, $dateFin, $clientId, $produitId, $vendeurId, $statut, $perPage, $offset);
        
        // Récupérer le nombre total de ventes pour la pagination
        $totalVentes = $this->venteModel->countFilteredVentes($dateDebut, $dateFin, $clientId, $produitId, $vendeurId, $statut);
        $totalPages = ceil($totalVentes / $perPage);
        
        // Récupérer les clients, produits et utilisateurs pour les filtres
        $clients = $this->clientModel->getAll();
        $products = $this->productModel->getAll();
        $users = isAdmin() ? $this->userModel->getAllByRole('vendeur') : [];
        
        // Charger la vue
        require_once 'views/ventes/index.php';
    }
    // Afficher les détails d'une vente
    public function show() {
        // Vérifier si l'utilisateur est connecté
        requireLogin();
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de vente non spécifié.');
            redirect('index.php?controller=vente&action=index');
        }
        
        $id = (int)$_GET['id'];
        
        // Récupérer la vente
        $vente = $this->venteModel->getById($id);
        
        if (!$vente) {
            setFlashMessage('error', 'Vente non trouvée.');
            redirect('index.php?controller=vente&action=index');
        }
        
        // Si l'utilisateur est un vendeur, vérifier qu'il est bien le vendeur de cette vente
        if (isVendeur() && $vente['vendeur_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Vous n\'êtes pas autorisé à voir cette vente.');
            redirect('index.php?controller=vente&action=index');
        }
        
        // Afficher la vue
        require_once 'views/ventes/show.php';
    }
    
    // Afficher le formulaire de création d'une vente
    public function create() {
        // Vérifier si l'utilisateur est connecté et a le droit de créer des ventes
        requireLogin();
        if (!isAdmin() && !isVendeur()) {
            setFlashMessage('error', 'Vous n\'êtes pas autorisé à créer des ventes.');
            redirect('index.php');
        }
        
        // Récupérer tous les produits et clients
        $products = $this->productModel->getAll();
        $clients = $this->clientModel->getAll();
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $produit_id = (int)$_POST['produit_id'];
            $client_id = (int)$_POST['client_id'];
            $quantite = (int)$_POST['quantite'];
            $mode_paiement = sanitize($_POST['mode_paiement']);
            
            // Récupérer les informations du produit
            $produit = $this->productModel->getById($produit_id);
            
            if (!$produit) {
                setFlashMessage('error', 'Produit non trouvé.');
                require_once 'views/ventes/create.php';
                return;
            }
            
            // Vérifier si le stock est suffisant
            if ($produit['quantite_stock'] < $quantite) {
                setFlashMessage('error', 'Stock insuffisant. Disponible: ' . $produit['quantite_stock'] . ' unité(s).');
                require_once 'views/ventes/create.php';
                return;
            }
            
            // Calculer le montant total
            $prix_unitaire = $produit['prix'];
            $montant_total = $prix_unitaire * $quantite;
            
            // Créer la vente
            if ($this->venteModel->create(
                $produit_id,
                $client_id,
                $_SESSION['user_id'],
                $quantite,
                $prix_unitaire,
                $montant_total,
                $mode_paiement,
                'complétée'
            )) {
                setFlashMessage('success', 'Vente enregistrée avec succès!');
                redirect('index.php?controller=vente&action=index');
            } else {
                setFlashMessage('error', 'Une erreur est survenue lors de l\'enregistrement de la vente.');
            }
        }
        
        // Afficher la vue
        require_once 'views/ventes/create.php';
    }
    
    // Annuler une vente
    public function cancel() {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de vente non spécifié.');
            redirect('index.php?controller=vente&action=index');
        }
        
        $id = (int)$_GET['id'];
        
        // Récupérer la vente
        $vente = $this->venteModel->getById($id);
        
        if (!$vente) {
            setFlashMessage('error', 'Vente non trouvée.');
            redirect('index.php?controller=vente&action=index');
        }
        
        // Vérifier si la vente est déjà annulée
        if ($vente['statut'] === 'annulée') {
            setFlashMessage('error', 'Cette vente est déjà annulée.');
            redirect('index.php?controller=vente&action=show&id=' . $id);
        }
        
        // Annuler la vente
        if ($this->venteModel->cancel($id)) {
            setFlashMessage('success', 'Vente annulée avec succès!');
        } else {
            setFlashMessage('error', 'Une erreur est survenue lors de l\'annulation de la vente.');
        }
        
        redirect('index.php?controller=vente&action=show&id=' . $id);
    }
    
    // Générer une facture pour une vente
    public function facture() {
        // Vérifier si l'utilisateur est connecté
        requireLogin();
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de vente non spécifié.');
            redirect('index.php?controller=vente&action=index');
        }
        
        $id = (int)$_GET['id'];
        
        // Récupérer la vente
        $vente = $this->venteModel->getById($id);
        
        if (!$vente) {
            setFlashMessage('error', 'Vente non trouvée.');
            redirect('index.php?controller=vente&action=index');
        }
        
        // Si l'utilisateur est un vendeur, vérifier qu'il est bien le vendeur de cette vente
        if (isVendeur() && $vente['vendeur_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'Vous n\'êtes pas autorisé à voir cette vente.');
            redirect('index.php?controller=vente&action=index');
        }
        
        // Afficher la vue de la facture
        require_once 'views/ventes/facture.php';
    }
    
    // Afficher les statistiques des ventes
    public function stats() {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');
        
        // Récupérer les statistiques
        $totalVentes = $this->venteModel->getTotalVentes();
        $statsByDay = $this->venteModel->getStatsByDay(30); // 30 derniers jours
        $statsByMonth = $this->venteModel->getStatsByMonth(12); // 12 derniers mois
        
        // Afficher la vue
        require_once 'views/ventes/stats.php';
    }
}
?>