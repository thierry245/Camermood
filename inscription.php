<?php
require_once __DIR__.'/includes/db_functions.php';
session_start();

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation simple
    if (empty($nom)) $erreurs['nom'] = "Nom requis";
    if (!$email) $erreurs['email'] = "Email invalide";
    if (strlen($password) < 8) $erreurs['password'] = "8 caractères minimum";
    if ($password !== $confirm_password) $erreurs['confirm_password'] = "Mots de passe différents";

    if (empty($erreurs)) {
        $pdo = getDbWrite();
        
        // Vérification si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $erreurs['email'] = "Email déjà utilisé";
        } else {
            // Hachage du mot de passe
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertion dans la base
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, password_hash) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$nom, $email, $hash])) {
                // Connexion automatique
                $_SESSION['user'] = [
                    'id' => $pdo->lastInsertId(),
                    'email' => $email,
                    'nom' => $nom,
                    'is_admin' => 0
                ];
                
                // Redirection
                header('Location: index.php');
                exit;
            } else {
                $erreurs['general'] = "Erreur lors de l'inscription";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="CSS/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - CamerMood</title>
</head>
<body>
    <!-- Votre header existant -->
    <header>
        <video autoplay muted loop class="background-video">
            <source src="video/pond_wouri.mp4" type="video/mp4">
        </video>
        <div class="header-content">
            <img class="onglet-image-logo" src="images/Logo_Camermood.png" alt="Logo CamerMood">
            <nav>
                <a href="index.php"><img src="images/Onglet_Acceuil.png" alt="Accueil"></a>
                <a href="hotels.php"><img src="images/Onglet_hotels.png" alt="Hôtels"></a>
                <a href="restaurant.php"><img class="onglet-image-restaurants" src="images/Onglet_restaurant.png" alt="Restaurants"></a>
                <a href="sites_touristiques.php"><img class="onglet-image-sites" src="images/Onglet_sites_touristique.png" alt="Sites touristiques"></a>
                <a href="festivals.php"><img class="onglet-image-festivals" src="images/Onglet_festivals.png" alt="Festivals"></a>
            </nav>
        </div>
    </header>

    <main class="form-container">
        <h1>Inscription</h1>
        
        <?php if(isset($erreurs['general'])): ?>
            <div class="error-message"><?= $erreurs['general'] ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="nom">Nom complet</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                <?php if(isset($erreurs['nom'])): ?>
                    <span class="error"><?= $erreurs['nom'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <?php if(isset($erreurs['email'])): ?>
                    <span class="error"><?= $erreurs['email'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
                <?php if(isset($erreurs['password'])): ?>
                    <span class="error"><?= $erreurs['password'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <?php if(isset($erreurs['confirm_password'])): ?>
                    <span class="error"><?= $erreurs['confirm_password'] ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-primary">S'inscrire</button>
        </form>

        <p class="auth-link">Déjà inscrit ? <a href="connexion.php">Connectez-vous</a></p>
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
</html>