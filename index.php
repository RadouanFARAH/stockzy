<?php
// Démarrer la session
session_start();

// Inclure les fichiers nécessaires
require_once 'config/database.php';
require_once 'utils/functions.php';
require_once 'utils/session.php';

// Définir le contrôleur et l'action par défaut
$controller = isset($_GET['controller']) ? sanitize($_GET['controller']) : 'dashboard';
$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'index';

// Charger le contrôleur approprié
$controllerFile = 'controllers/' . ucfirst($controller) . 'Controller.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = ucfirst($controller) . 'Controller';
    $controllerInstance = new $controllerClass();

    // Vérifier si l'action existe
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        // Action non trouvée
        require_once 'error/404.php';
    }
} else {
    // Contrôleur non trouvé
    require_once 'error/404.php';

}
