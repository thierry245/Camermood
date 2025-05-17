<?php
require_once __DIR__.'/../config/db_config.php';

/**
 * Obtient une connexion PDO pour les opérations de lecture
 */
function getDbRead() {
    try {
        $dsn = "mysql:dbname=".BDSCHEMA.";host=".BDSERVEUR;
        return new PDO($dsn, BDUTILISATEUR_LECTURE, BDMDP, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Erreur de connexion DB (lecture): " . $e->getMessage());
    }
}

/**
 * Obtient une connexion PDO pour les opérations d'écriture
 */
function getDbWrite() {
    try {
        $dsn = "mysql:dbname=".BDSCHEMA.";host=".BDSERVEUR;
        return new PDO($dsn, BDUTILISATEUR_ECRITURE, BDMDP, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Erreur de connexion DB (écriture): " . $e->getMessage());
    }
}


/**
 * Récupère toutes les réservations
 */
function getAllReservations() {
    try {
        $pdo = getDbRead();
        $stmt = $pdo->prepare("
            SELECT r.*, u.nom as utilisateur_nom, u.email 
            FROM reservations r
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            ORDER BY r.date_creation DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur getAllReservations: " . $e->getMessage());
        return [];
    }
}

/**
 * Crée une nouvelle réservation
 */
function createReservation($userId, $lieuId, $dateArrivee, $dateDepart, $nbPersonnes) {
    try {
        $pdo = getDbWrite();
        $stmt = $pdo->prepare("
            INSERT INTO reservations 
            (utilisateur_id, lieu_id, date_arrivee, date_depart, nb_personnes) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $lieuId, $dateArrivee, $dateDepart, $nbPersonnes]);
    } catch (PDOException $e) {
        error_log("Erreur createReservation: " . $e->getMessage());
        return false;
    }
}

function checkAdminAccess() {
    session_start();
    if (!isset($_SESSION['user']['id']) || !isAdmin($_SESSION['user']['id'])) {
        header('HTTP/1.0 403 Forbidden');
        die('Accès interdit');
    }
}
function getUserById($id) {
    $pdo = getDbRead(); // ou getDbWrite() si c'est la même connexion
    $stmt = $pdo->prepare("SELECT id, nom, email, is_admin FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function isAdmin($userId) {
    $user = getUserById($userId);
    return $user && $user['is_admin'] == 1;
}

/**
 * Récupère toutes les destinations d'un type spécifique
 */
function getDestinationsByType($type) {
    try {
        $pdo = getDbRead();
        $stmt = $pdo->prepare("SELECT * FROM destinations WHERE type = ? ORDER BY nom");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur getDestinationsByType: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère une destination par son ID
 */
function getDestinationById($id) {
    try {
        $pdo = getDbRead();
        $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erreur getDestinationById: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les réservations d'un utilisateur
 */
function getUserReservations($userId) {
    try {
        $pdo = getDbRead();
        $stmt = $pdo->prepare("
            SELECT r.*, d.nom as lieu_nom, d.type as lieu_type, d.image as lieu_image
            FROM reservations r
            JOIN destinations d ON r.lieu_id = d.id
            WHERE r.utilisateur_id = :userId
            ORDER BY r.date_creation DESC
        ");
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur getUserReservations: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère toutes les réservations (pour admin)
 */
function getAllReservationsWithUsers() {
    try {
        $pdo = getDbRead();
        $stmt = $pdo->prepare("
            SELECT r.*, d.nom as lieu_nom, d.type as lieu_type, 
                   u.nom as user_nom, u.email as user_email
            FROM reservations r
            JOIN destinations d ON r.lieu_id = d.id
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            ORDER BY r.date_creation DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur getAllReservationsWithUsers: " . $e->getMessage());
        return [];
    }
}
function fixEncoding($str) {
    return mb_convert_encoding($str, 'UTF-8', 'Windows-1252');
}
?>