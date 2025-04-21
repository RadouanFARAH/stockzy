<?php
require_once 'models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // Afficher le formulaire de connexion
    public function login() {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if (isLoggedIn()) {
            redirect('index.php?controller=dashboard');
        }

        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            
            // Vérifier si l'email existe
            $user = $this->userModel->getByEmail($email);
            
            if ($user && verifyPassword($password, $user['mot_de_passe'])) {
                // Connexion réussie, créer la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                redirect('index.php?controller=dashboard');
            } else {
                // Échec de la connexion
                setFlashMessage('error', 'Email ou mot de passe incorrect.');
            }
        }
        // Afficher la vue de connexion
        require_once 'views/auth/login.php';
    }
    
    // Déconnexion
    public function logout() {
        // Détruire toutes les variables de session
        session_unset();
        
        // Détruire la session
        session_destroy();
        
        // Rediriger vers la page de connexion
        redirect('index.php?controller=auth&action=login');
    }
}
?>