<?php
require_once __DIR__.'/../includes/db_functions.php';
require_once __DIR__.'/../config/session_config.php';
require_once __DIR__.'/DoubleAuth.controller.php';
require_once __DIR__.'/../includes/logging_functions.php';

session_start();

try {
    // Vérification des données avec filter_input
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (empty($email)) {
        header('Location: ../connexion.php?error=missing');
        exit;
    }

    if (!$email) {
        header('Location: ../connexion.php?error=invalid_email');
        exit;
    }

    // Vérification des identifiants
    error_log("Début de auth.process.php");

    $pdo = getDbRead();
    $stmt = $pdo->prepare("SELECT id, password_hash, nom, email, is_admin FROM utilisateurs WHERE email = :email");
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
    logToFile('acces-refuses.log', "Tentative de connexion échouée pour l'email: $email");
    header('Location: ../connexion.php?error=invalid');
    exit;
    }

    // Après la vérification réussie
    logToFile('acces-reussis.log', "Connexion réussie pour l'utilisateur ID: {$user['id']} avec l'email: $email");

    error_log("Utilisateur valide: ID " . $user['id']);

    $doubleAuth = new DoubleAuth();
    $code = $doubleAuth->genererCode($user['id'], $email);
    error_log("Code généré pour user {$user['id']}: $code");

    // Simule l'envoi
    error_log("Code de vérif pour $email: $code");

    // Stockage temporaire
    $_SESSION['temp_auth'] = [
        'user_id' => $user['id'],
        'user_nom' => $user['nom'] ?? '',
        'email' => $user['email'],
        'ip' => $_SERVER['REMOTE_ADDR'],
        'attempts' => 0,
        'expires' => time() + 300
    ];

    header('Location: ../verification.php');
    exit;

} catch (Exception $e) {
    error_log("Erreur dans auth.process.php: " . $e->getMessage());
    header('Location: ../connexion.php?error=internal');
    exit;
}