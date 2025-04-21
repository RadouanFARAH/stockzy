<?php
require_once 'models/Product.php';
require_once 'models/Vente.php';
require_once 'models/User.php';
require_once 'models/Client.php';
require_once 'models/Category.php';
class DashboardController {
    private $productModel;
    private $venteModel;
    private $userModel;
    private $clientModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->venteModel = new Vente();
        $this->userModel = new User();
        $this->clientModel = new Client();
        $this->categoryModel = new Category();
    }
    
    public function index() {
        // Vérifier si l'utilisateur est connecté
        requireLogin();
        
        // Récupérer les statistiques générales
        $totalProducts = $this->productModel->getTotalCount();
        $lowStockProducts = $this->productModel->getLowStockProducts();
        $totalVentes = $this->venteModel->getTotalVentes();
        $totalClients = $this->clientModel->getTotalCount();
        
        // Récupérer les statistiques de ventes
        $ventesByDay = $this->venteModel->getStatsByDay(7); // 7 derniers jours
        $ventesByMonth = $this->venteModel->getStatsByMonth(6); // 6 derniers mois
        
        // Récupérer les produits les plus vendus
        $topProducts = $this->productModel->getTopSellingProducts(5);
        
        // Récupérer les dernières ventes
        if (isAdmin()) {
            $recentVentes = $this->venteModel->getRecentVentes(5);
        } elseif (isVendeur()) {
            $recentVentes = $this->venteModel->getRecentVentesByVendeur($_SESSION['user_id'], 5);
        } else {
            $recentVentes = [];
        }
        
        // Récupérer les statistiques spécifiques au rôle
        if (isAdmin()) {
            $totalUsers = $this->userModel->getTotalCount();
            $totalFournisseurs = $this->userModel->getTotalCountFournisseurs();
            $totalCategories = $this->categoryModel->getTotalCount();
            $userStats = $this->userModel->getUserStats();
        } elseif (isVendeur()) {
            $vendeurVentes = $this->venteModel->getStatsByVendeur($_SESSION['user_id']);
            $vendeurClients = $this->clientModel->getClientsByVendeur($_SESSION['user_id']);
        } elseif (isFournisseur()) {
            $fournisseurProducts = $this->productModel->getProductsByFournisseur($_SESSION['user_id']);
            $fournisseurVentes = $this->venteModel->getVentesByFournisseur($_SESSION['user_id']);
        }
        
        // Charger la vue du tableau de bord
        require_once 'views/dashboard/index.php';
    }
}
?>