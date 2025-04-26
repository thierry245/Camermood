<?php
require_once __DIR__.'/../config/session_config.php';

function verifierSession() {
    session_start();
    
    // Vérifie si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        header('Location: connexion.php?session=expired');
        exit;
    }
    
    // Vérifie l'expiration de la session
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_DUREE)) {
        // Session expirée
        session_unset();
        session_destroy();
        header('Location: connexion.php?session=expired');
        exit;
    }
    
    // Met à jour le timestamp de dernière activité
    $_SESSION['last_activity'] = time();
}

function deconnecterUtilisateur() {
    session_start();
    
    // Effacer toutes les données de session
    $_SESSION = array();
    
    // Supprimer le cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // Détruire la session
    session_destroy();
}