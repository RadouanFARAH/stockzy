<?php
// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour vérifier le rôle de l'utilisateur
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    return $_SESSION['user_role'] === $role;
}

// Fonction pour vérifier si l'utilisateur est administrateur
function isAdmin() {
    return hasRole('admin');
}

// Fonction pour vérifier si l'utilisateur est vendeur
function isVendeur() {
    return hasRole('vendeur');
}

// Fonction pour vérifier si l'utilisateur est fournisseur
function isFournisseur() {
    return hasRole('fournisseur');
}

// Fonction pour rediriger si l'utilisateur n'est pas connecté
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('index.php?controller=auth&action=login');
    }
}

// Fonction pour rediriger si l'utilisateur n'a pas le rôle requis
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        setFlashMessage('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
        redirect('index.php?controller=dashboard');
    }
}
?>