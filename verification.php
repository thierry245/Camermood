<?php
require_once __DIR__.'/includes/db_functions.php';
require_once __DIR__.'/config/session_config.php';
require_once __DIR__.'/controllers/DoubleAuth.controller.php';

session_start();

// Vérifie si un processus d'authentification est en cours
if (!isset($_SESSION['temp_auth'])) {
    header('Location: connexion.php');
    exit;
}

// Vérifie si l'IP actuelle correspond à celle stockée dans la session temporaire
if ($_SESSION['temp_auth']['ip'] !== $_SERVER['REMOTE_ADDR']) {
    session_destroy();
    header('Location: connexion.php?error=hijack');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
        
        if (empty($code)) {
            throw new Exception("Code vide");
        }

        $doubleAuth = new DoubleAuth();
        
        if ($doubleAuth->verifierCode($_SESSION['temp_auth']['user_id'], $code)) {
            // Récupère les infos complètes de l'utilisateur depuis la base
            $user = getUserById($_SESSION['temp_auth']['user_id']);

            if ($user) {
                $_SESSION['user'] = [
                    'id' => $_SESSION['temp_auth']['user_id'],
                    'nom' => $_SESSION['temp_auth']['user_nom'],
                    'email' => $_SESSION['temp_auth']['email'],
                    'is_admin' => $user['is_admin'] ?? 0,
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'last_activity' => time()
                ];
                error_log("Session user créée : " . print_r($_SESSION['user'], true));

                // Nettoie la session temporaire
                unset($_SESSION['temp_auth']);
               
                // Supprime le code de vérification dans la base
                $pdo = getDbWrite();
                $stmt = $pdo->prepare("UPDATE utilisateurs SET code_verification = NULL WHERE id = :userId");
                $stmt->bindValue(':userId', $user['id'], PDO::PARAM_INT);
                $stmt->execute();
                
                session_write_close();
                header('Location: index.php');
                exit;
            } else {
                $erreur = "Erreur lors de la récupération des données utilisateur.";
            }
        } else {
            $erreur = "Code invalide";
            $_SESSION['temp_auth']['attempts']++;

            if ($_SESSION['temp_auth']['attempts'] >= 3) {
                session_destroy();
                header('Location: connexion.php?error=blocked');
                exit;
            }
        }
    } catch (Exception $e) {
        $erreur = "Erreur de traitement: " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification - CamerMood</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>
    
    <header>
        <video autoplay muted loop class="background-video">
            <source src="video/pond_wouri.mp4" type="video/mp4">
        </video>
        <div class="header-content">
            <img class="onglet-image-logo" src="images/Logo_Camermood.png" alt="Logo CamerMood">
            <nav>
                <a href="index.php">
                    <img src="images\Onglet_Acceuil.png" alt="Accueil">
                </a>
                <a href="hotels.php">
                    <img src="images\Onglet_hotels.png" alt="Hôtels">
                </a>
                <a href="restaurant.php">
                    <img  class="onglet-image-restaurants" src="images\Onglet_restaurant.png" alt="Restaurants">
                </a>
                <a href="sites_touristiques.php">
                    <img class="onglet-image-sites" src="images\Onglet_sites_touristique.png" alt="Sites touristiques">
                </a>
                <a href="festivals.php">
                    <img class="onglet-image-festivals" src="images\Onglet_festivals.png" alt="Festivals">
                </a>
            </nav>
        </div>
    </header>

    <main class="form-container">
        <h1>Vérification en 2 étapes</h1>
        
        <?php if($erreur): ?>
            <div class="alert error"><?= $erreur ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="code">Code à 6 chiffres</label>
                <input type="text" id="code" name="code" 
                       pattern="\d{6}" 
                       title="Entrez les 6 chiffres reçus" 
                       required>
            </div>
            <button type="submit" class="btn-primary">Vérifier</button>
        </form>
    </main>


    <footer class="booking-footer">
            <div class="footer-container">
                <div class="footer-section">
                    <h4>Destinations populaires</h4>
                    <ul>
                        <li><a href="#">Yaoundé</a></li>
                        <li><a href="#">Douala</a></li>
                        <li><a href="#">Bafoussam</a></li>
                        <li><a href="#">Limbe</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Hébergements</h4>
                    <ul>
                        <li><a href="#">Hôtels</a></li>
                        <li><a href="#">Appartements</a></li>
                        <li><a href="#">Villas</a></li>
                        <li><a href="#">Auberges</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>À propos</h4>
                    <ul>
                        <li><a href="#">Qui sommes-nous ?</a></li>
                        <li><a href="#">Carrières</a></li>
                        <li><a href="#">Presse</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Assistance</h4>
                    <ul>
                        <li><a href="#">Centre d'aide</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                        <li><a href="#">Conditions générales</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="social-icons">
                    <a href="#"><img src="images/facebook-icon.jpeg" alt="Facebook"></a>
                    <a href="#"><img src="images/twitter-icon.jpeg" alt="Twitter"></a>
                    <a href="#"><img src="images/instagram-icon.jpeg" alt="Instagram"></a>
                </div>
                <div class="copyright">
                    <p>&copy; 2024 CamerMood. Tous droits réservés.</p>
                </div>
            </div>
        </footer>

</body>

