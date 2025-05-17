<?php
require_once __DIR__.'/includes/db_functions.php';
require_once __DIR__.'/includes/session_functions.php';

session_start();
verifierSession();

// Debug initial
error_log("Début annulation - User: ".$_SESSION['user']['id']);

if (!isset($_GET['id'])) {
    error_log("Erreur: Pas d'ID");
    header('Location: index.php?error=no_id');
    exit;
}

$reservationId = (int)$_GET['id'];

try {
    $pdo = getDbWrite();
    
    // 1. Vérification hexadécimale du statut
    $check = $pdo->prepare("SELECT id, utilisateur_id, statut, HEX(statut) as statut_hex FROM reservations WHERE id = ?");
    $check->execute([$reservationId]);
    $resData = $check->fetch();

    if (!$resData) {
        error_log("Réservation introuvable: ".$reservationId);
        header('Location: index.php?error=not_found');
        exit;
    }

    error_log("Statut HEX: ".$resData['statut_hex']); // Debug crucial

    // 2. Comparaison hexadécimale
    $isConfirmed = ($resData['statut_hex'] === '636F6E6669726DC3A965'); // UTF-8 pour "confirmée"
    
    if (!$isConfirmed) {
        error_log("Statut non confirmé: ".$resData['statut']." (HEX: ".$resData['statut_hex'].")");
        header('Location: index.php?error=invalid_status');
        exit;
    }

    // 3. Mise à jour en forçant l'encodage
    $update = $pdo->prepare("UPDATE reservations SET statut = _utf8mb4'annulée' WHERE id = ?");
    $update->execute([$reservationId]);
    
    error_log("Annulation réussie pour: ".$reservationId);
    header('Location: index.php?show_reservations=1&cancel_success=1');

} catch (PDOException $e) {
    error_log("Erreur SQL complète: ".$e->__toString());
    header('Location: index.php?error=db_error');
}