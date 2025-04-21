<?php
require_once 'models/Product.php';
require_once 'models/Category.php';
require_once 'models/User.php';

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $userModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->userModel = new User();
    }

    // Afficher la liste des produits
    public function index()
    {
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
        $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
        $categoryId = isset($_GET['category']) ? intval($_GET['category']) : 0;
        $fournisseurId = isset($_GET['utilisateur']) ? intval($_GET['utilisateur']) : 0;
        $stockFilter = isset($_GET['stock']) ? sanitize($_GET['stock']) : '';
        // Si l'utilisateur n'est pas admin, limiter aux produits de l'utilisateur connecté
        if (!isAdmin()) {
            $fournisseurId = $_SESSION['user_id'];
        }
        // Pagination
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // Construire la chaîne de requête pour la pagination
        $queryParams = [];
        if (!empty($search)) $queryParams[] = 'search=' . urlencode($search);
        if ($categoryId > 0) $queryParams[] = 'category=' . $categoryId;
        if ($fournisseurId > 0) $queryParams[] = 'utilisateur=' . $fournisseurId;
        if (!empty($stockFilter)) $queryParams[] = 'stock=' . urlencode($stockFilter);
        $queryString = !empty($queryParams) ? '&' . implode('&', $queryParams) : '';


        // Récupérer les produits filtrés et paginés
        $products = $this->productModel->getFilteredProducts($search, $categoryId, $fournisseurId, $stockFilter, $perPage, $offset);

        // Récupérer le nombre total de produits pour la pagination
        $totalProducts = $this->productModel->countFilteredProducts($search, $categoryId, $fournisseurId, $stockFilter);
        $totalPages = ceil($totalProducts / $perPage);

        // Récupérer les catégories et fournisseurs pour les filtres
        $categories = $this->categoryModel->getAll();
        $fournisseurs = $this->userModel->getAllFournisseurs();

        // Charger la vue
        require_once 'views/products/index.php';
    }

    // Afficher les détails d'un produit
    public function show()
    {
        // Vérifier si l'utilisateur est connecté
        requireLogin();

        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de produit non spécifié.');
            redirect('index.php?controller=product&action=index');
        }

        $id = (int)$_GET['id'];

        // Récupérer le produit
        $product = $this->productModel->getById($id);

        if (!$product) {
            setFlashMessage('error', 'Produit non trouvé.');
            redirect('index.php?controller=product&action=index');
        }

        // Afficher la vue
        require_once 'views/products/show.php';
    }

    // Afficher le formulaire de création d'un produit
    public function create()
    {
        // Vérifier si l'utilisateur est administrateur ou fournisseur
        if (!isAdmin() && !isFournisseur()) {
            requireRole('admin');
        }

        // Récupérer toutes les catégories
        $categories = $this->categoryModel->getAll();

        // Si l'utilisateur est fournisseur, définir son ID comme fournisseur par défaut
        $fournisseurs = [];
        $defaultFournisseurId = null;
        if (isFournisseur()) {
            $defaultFournisseurId = $_SESSION['user_id'];
            $fournisseurs[] = $this->userModel->getById($defaultFournisseurId); // Fetch only the connected fournisseur
        } else {
            $fournisseurs = $this->userModel->getAllFournisseurs();
        }

        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = sanitize($_POST['nom']);
            $description = sanitize($_POST['description']);
            $prix = (float)$_POST['prix'];
            $quantite_stock = (int)$_POST['quantite_stock'];
            $seuil_alerte = (int)$_POST['seuil_alerte'];
            $categorie_id = !empty($_POST['categorie_id']) ? (int)$_POST['categorie_id'] : null;
            $fournisseur_id = isFournisseur() ? $_SESSION['user_id'] : (!empty($_POST['fournisseur_id']) ? (int)$_POST['fournisseur_id'] : null);

            // Traitement de l'image
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'assets/img/products/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $upload_path;
                }
            }

            // Valider les données
            $errors = [];
            if (empty($nom)) {
                $errors[] = "Le nom du produit est requis.";
            }
            if ($prix <= 0) {
                $errors[] = "Le prix doit être supérieur à 0.";
            }
            if ($quantite_stock < 0) {
                $errors[] = "La quantité en stock ne peut pas être négative.";
            }
            if ($seuil_alerte < 0) {
                $errors[] = "Le seuil d'alerte ne peut pas être négatif.";
            }

            // S'il n'y a pas d'erreurs, créer le produit
            if (empty($errors)) {
                if ($this->productModel->create($nom, $description, $prix, $quantite_stock, $seuil_alerte, $image, $categorie_id, $fournisseur_id)) {
                    setFlashMessage('success', 'Produit créé avec succès!');
                    redirect('index.php?controller=product&action=index');
                } else {
                    setFlashMessage('error', 'Une erreur est survenue lors de la création du produit.');
                }
            } else {
                foreach ($errors as $error) {
                    setFlashMessage('error', $error);
                }
            }
        }

        // Afficher la vue
        require_once 'views/products/create.php';
    }

    // Afficher le formulaire de modification d'un produit
    public function edit()
    {
        // Vérifier si l'utilisateur est administrateur ou fournisseur
        if (!isAdmin() && !isFournisseur()) {
            requireRole('admin');
        }

        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de produit non spécifié.');
            redirect('index.php?controller=product&action=index');
        }

        $id = (int)$_GET['id'];

        // Récupérer le produit
        $product = $this->productModel->getById($id);

        if (!$product) {
            setFlashMessage('error', 'Produit non trouvé.');
            redirect('index.php?controller=product&action=index');
        }

        // Récupérer toutes les catégories
        $categories = $this->categoryModel->getAll();

        // Si l'utilisateur est fournisseur, définir son ID comme fournisseur par défaut
        $fournisseurs = [];
        $defaultFournisseurId = null;
        if (isFournisseur()) {
            $defaultFournisseurId = $_SESSION['user_id'];
            $fournisseurs[] = $this->userModel->getById($defaultFournisseurId); // Fetch only the connected fournisseur
        } else {
            $fournisseurs = $this->userModel->getAllFournisseurs();
        }

        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = sanitize($_POST['nom']);
            $description = sanitize($_POST['description']);
            $prix = (float)$_POST['prix'];
            $quantite_stock = (int)$_POST['quantite_stock'];
            $seuil_alerte = (int)$_POST['seuil_alerte'];
            $categorie_id = !empty($_POST['categorie_id']) ? (int)$_POST['categorie_id'] : null;
            $fournisseur_id = isFournisseur() ? $_SESSION['user_id'] : (!empty($_POST['fournisseur_id']) ? (int)$_POST['fournisseur_id'] : null);

            // Traitement de l'image
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'assets/img/products/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $upload_path;

                    // Supprimer l'ancienne image si elle existe
                    if (!empty($product['image']) && file_exists($product['image'])) {
                        unlink($product['image']);
                    }
                }
            }

            // Valider les données
            $errors = [];
            if (empty($nom)) {
                $errors[] = "Le nom du produit est requis.";
            }
            if ($prix <= 0) {
                $errors[] = "Le prix doit être supérieur à 0.";
            }
            if ($quantite_stock < 0) {
                $errors[] = "La quantité en stock ne peut pas être négative.";
            }
            if ($seuil_alerte < 0) {
                $errors[] = "Le seuil d'alerte ne peut pas être négatif.";
            }

            // S'il n'y a pas d'erreurs, mettre à jour le produit
            if (empty($errors)) {
                if ($this->productModel->update($id, $nom, $description, $prix, $quantite_stock, $seuil_alerte, $image, $categorie_id, $fournisseur_id)) {
                    setFlashMessage('success', 'Produit mis à jour avec succès!');
                    redirect('index.php?controller=product&action=index');
                } else {
                    setFlashMessage('error', 'Une erreur est survenue lors de la mise à jour du produit.');
                }
            } else {
                foreach ($errors as $error) {
                    setFlashMessage('error', $error);
                }
            }
        }

        // Afficher la vue
        require_once 'views/products/edit.php';
    }

    // Supprimer un produit
    public function delete()
    {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');

        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de produit non spécifié.');
            redirect('index.php?controller=product&action=index');
        }

        $id = (int)$_GET['id'];

        // Récupérer le produit pour obtenir le chemin de l'image
        $product = $this->productModel->getById($id);

        if (!$product) {
            setFlashMessage('error', 'Produit non trouvé.');
            redirect('index.php?controller=product&action=index');
        }

        // Supprimer le produit
        if ($this->productModel->delete($id)) {
            // Supprimer l'image si elle existe
            if (!empty($product['image']) && file_exists($product['image'])) {
                unlink($product['image']);
            }

            setFlashMessage('success', 'Produit supprimé avec succès!');
        } else {
            setFlashMessage('error', 'Impossible de supprimer ce produit car il est associé à des ventes.');
        }

        redirect('index.php?controller=product&action=index');
    }

    // Afficher les produits en alerte de stock
    public function stockAlerts()
    {
        // Vérifier si l'utilisateur est connecté
        requireLogin();

        // Récupérer les produits en alerte de stock
        $products = $this->productModel->getStockAlerts();

        // Afficher la vue
        require_once 'views/products/stock_alerts.php';
    }

    // Mettre à jour le stock d'un produit
    public function updateStock()
    {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');

        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de produit non spécifié.');
            redirect('index.php?controller=product&action=index');
        }

        $id = (int)$_GET['id'];

        // Récupérer le produit
        $product = $this->productModel->getById($id);

        if (!$product) {
            setFlashMessage('error', 'Produit non trouvé.');
            redirect('index.php?controller=product&action=index');
        }

        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $quantite = (int)$_POST['quantite'];

            // Mettre à jour le stock
            if ($this->productModel->updateStock($id, $quantite)) {
                setFlashMessage('success', 'Stock mis à jour avec succès!');
                redirect('index.php?controller=product&action=show&id=' . $id);
            } else {
                setFlashMessage('error', 'Une erreur est survenue lors de la mise à jour du stock.');
            }
        }

        // Afficher la vue
        require_once 'views/products/update_stock.php';
    }
}
