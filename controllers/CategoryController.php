<?php
require_once 'models/Category.php';

class CategoryController {
    private $categoryModel;
    
    public function __construct() {
        $this->categoryModel = new Category();
    }
    
    // Afficher la liste des catégories
    public function index() {
        // Vérifier si l'utilisateur est connecté
        requireLogin();
        
        // Récupérer toutes les catégories
        $categories = $this->categoryModel->getAll();
        
        // Pour chaque catégorie, compter le nombre de produits
        foreach ($categories as $key => $category) {
            $categories[$key]['product_count'] = $this->categoryModel->countProducts($category['id']);
        }
        
        // Afficher la vue
        require_once 'views/categories/index.php';
    }
    
    // Afficher le formulaire de création d'une catégorie
    public function create() {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = sanitize($_POST['nom']);
            $description = sanitize($_POST['description']);
            
            // Valider les données
            $errors = [];
            
            if (empty($nom)) {
                $errors[] = "Le nom de la catégorie est requis.";
            } elseif ($this->categoryModel->existsByName($nom)) {
                $errors[] = "Une catégorie avec ce nom existe déjà.";
            }
            
            // S'il n'y a pas d'erreurs, créer la catégorie
            if (empty($errors)) {
                if ($this->categoryModel->create($nom, $description)) {
                    setFlashMessage('success', 'Catégorie créée avec succès!');
                    redirect('index.php?controller=category&action=index');
                } else {
                    setFlashMessage('error', 'Une erreur est survenue lors de la création de la catégorie.');
                }
            } else {
                // Afficher les erreurs
                foreach ($errors as $error) {
                    setFlashMessage('error', $error);
                }
            }
        }
        
        // Afficher la vue
        require_once 'views/categories/create.php';
    }
    
    // Afficher le formulaire de modification d'une catégorie
    public function edit() {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de catégorie non spécifié.');
            redirect('index.php?controller=category&action=index');
        }
        
        $id = (int)$_GET['id'];
        
        // Récupérer la catégorie
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            setFlashMessage('error', 'Catégorie non trouvée.');
            redirect('index.php?controller=category&action=index');
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = sanitize($_POST['nom']);
            $description = sanitize($_POST['description']);
            
            // Valider les données
            $errors = [];
            
            if (empty($nom)) {
                $errors[] = "Le nom de la catégorie est requis.";
            } elseif ($nom !== $category['nom'] && $this->categoryModel->existsByName($nom)) {
                $errors[] = "Une catégorie avec ce nom existe déjà.";
            }
            
            // S'il n'y a pas d'erreurs, mettre à jour la catégorie
            if (empty($errors)) {
                if ($this->categoryModel->update($id, $nom, $description)) {
                    setFlashMessage('success', 'Catégorie mise à jour avec succès!');
                    redirect('index.php?controller=category&action=index');
                } else {
                    setFlashMessage('error', 'Une erreur est survenue lors de la mise à jour de la catégorie.');
                }
            } else {
                // Afficher les erreurs
                foreach ($errors as $error) {
                    setFlashMessage('error', $error);
                }
            }
        }
        
        // Afficher la vue
        require_once 'views/categories/edit.php';
    }
    
    // Supprimer une catégorie
    public function delete() {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de catégorie non spécifié.');
            redirect('index.php?controller=category&action=index');
        }
        
        $id = (int)$_GET['id'];
        
        // Supprimer la catégorie
        if ($this->categoryModel->delete($id)) {
            setFlashMessage('success', 'Catégorie supprimée avec succès!');
        } else {
            setFlashMessage('error', 'Impossible de supprimer cette catégorie car elle est utilisée par des produits.');
        }
        
        redirect('index.php?controller=category&action=index');
    }
}
?>