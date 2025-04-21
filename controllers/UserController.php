<?php
require_once 'models/User.php';

class UserController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // Afficher la liste des utilisateurs
    public function index() {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');
        
        // Récupérer tous les utilisateurs
        $users = $this->userModel->getAll();
        
        // Afficher la vue
        require_once 'views/users/index.php';
    }
    
    // Afficher le formulaire de création d'un utilisateur
    public function create() {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = sanitize($_POST['nom']);
            $email = sanitize($_POST['email']);
            $mot_de_passe = $_POST['mot_de_passe'];
            $role = sanitize($_POST['role']);
            
            // Valider les données
            $errors = [];
            
            if (empty($nom)) {
                $errors[] = "Le nom est requis.";
            }
            
            if (empty($email)) {
                $errors[] = "L'email est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            } elseif ($this->userModel->getByEmail($email)) {
                $errors[] = "Cet email est déjà utilisé.";
            }
            
            if (empty($mot_de_passe)) {
                $errors[] = "Le mot de passe est requis.";
            } elseif (strlen($mot_de_passe) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
            }
            
            if (empty($role)) {
                $errors[] = "Le rôle est requis.";
            } elseif (!in_array($role, ['admin', 'vendeur', 'fournisseur'])) {
                $errors[] = "Le rôle n'est pas valide.";
            }
            if ($this->userModel->getByEmail($email)) {
                $errors[] = "Cet email est déjà utilisé.";
            }
            
            
            // S'il n'y a pas d'erreurs, créer l'utilisateur
            if (empty($errors)) {
                if ($this->userModel->create($nom, $email, $mot_de_passe, $role)) {
                    setFlashMessage('success', 'Utilisateur créé avec succès!');
                    redirect('index.php?controller=user&action=index');
                } else {
                    setFlashMessage('error', 'Une erreur est survenue lors de la création de l\'utilisateur.');
                }
            } else {
                // Afficher les erreurs
                foreach ($errors as $error) {
                    setFlashMessage('error', $error);
                }
            }
        }
        
        // Afficher la vue
        require_once 'views/users/create.php';
    }
    
    // Afficher le formulaire de modification d'un utilisateur
    public function edit() {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID d\'utilisateur non spécifié.');
            redirect('index.php?controller=user&action=index');
        }
        
        $id = (int)$_GET['id'];
        
        // Récupérer l'utilisateur
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            setFlashMessage('error', 'Utilisateur non trouvé.');
            redirect('index.php?controller=user&action=index');
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = sanitize($_POST['nom']);
            $email = sanitize($_POST['email']);
            $role = sanitize($_POST['role']);
            
            // Valider les données
            $errors = [];
            
            if (empty($nom)) {
                $errors[] = "Le nom est requis.";
            }
            
            if (empty($email)) {
                $errors[] = "L'email est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            } elseif ($email !== $user['email'] && $this->userModel->getByEmail($email)) {
                $errors[] = "Cet email est déjà utilisé.";
            }
            
            if (empty($role)) {
                $errors[] = "Le rôle est requis.";
            } elseif (!in_array($role, ['admin', 'vendeur', 'fournisseur'])) {
                $errors[] = "Le rôle n'est pas valide.";
            }
            
            // S'il n'y a pas d'erreurs, mettre à jour l'utilisateur
            if (empty($errors)) {
                if ($this->userModel->update($id, $nom, $email, $role)) {
                    setFlashMessage('success', 'Utilisateur mis à jour avec succès!');
                    redirect('index.php?controller=user&action=index');
                } else {
                    setFlashMessage('error', 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.');
                }
            } else {
                // Afficher les erreurs
                foreach ($errors as $error) {
                    setFlashMessage('error', $error);
                }
            }
        }
        
        // Afficher la vue
        require_once 'views/users/edit.php';
    }
    
    // Supprimer un utilisateur
    public function delete() {
        // Vérifier si l'utilisateur est administrateur
        requireRole('admin');
        
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID d\'utilisateur non spécifié.');
            redirect('index.php?controller=user&action=index');
        }
        
        $id = (int)$_GET['id'];
        
        // Empêcher la suppression de son propre compte
        if ($id === (int)$_SESSION['user_id']) {
            setFlashMessage('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            redirect('index.php?controller=user&action=index');
        }
        
        // Supprimer l'utilisateur
        if ($this->userModel->delete($id)) {
            setFlashMessage('success', 'Utilisateur supprimé avec succès!');
        } else {
            setFlashMessage('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur.');
        }
        
        redirect('index.php?controller=user&action=index');
    }
}
?>