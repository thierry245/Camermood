<?php
require_once __DIR__.'/../includes/db_functions.php';
require_once __DIR__.'/../config/session_config.php';
require_once __DIR__.'/DoubleAuth.controller.php';

session_start();

// Vérification des données
if (empty($_POST['email']) || empty($_POST['password'])) {
    header('Location: ../connexion.php?error=missing');
    exit;
}

$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$password = $_POST['password'];

if (!$email) {
    header('Location: ../connexion.php?error=invalid_email');
    exit;
}

// Vérification des identifiants
error_log("Début de auth.process.php");

$pdo = getDbRead();
$stmt = $pdo->prepare("SELECT id, password_hash, nom, email, is_admin FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    error_log("Tentative de connexion échouée pour: $email");
    header('Location: ../connexion.php?error=invalid');
    exit;
}

try {
    error_log("Utilisateur valide: ID " . $user['id']);

    $doubleAuth = new DoubleAuth();
    $code = $doubleAuth->genererCode($user['id'], $email);
    error_log("Code généré pour user {$user['id']}: $code");

    // Simule l'envoi
    error_log("Code de vérif pour $email: $code");
} catch (Exception $e) {
    error_log("Erreur DoubleAuth : " . $e->getMessage());
    die("Erreur interne, réessayez plus tard.");
}


// Envoi du code (simulé)
error_log("Code de vérification pour $email: $code");

// Stockage temporaire
$_SESSION['temp_auth'] = [
   'user_id' => $user['id'],
    'user_nom' => $user['nom'] ?? '', //  Gère le cas où 'nom' est absent
    'email' => $user['email'], //  l'email si nécessaire
    'ip' => $_SERVER['REMOTE_ADDR'],
    'attempts' => 0,
    'expires' => time() + 300 // 5 minutes d'expiration
];

header('Location: ../verification.php');
exit;