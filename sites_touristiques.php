<?php
require_once __DIR__.'/includes/db_functions.php';

// Récupérer les sites touristiques  depuis la base de données
$type = 'site-touristique';
$destinationsFiltrees = getDestinationsByType($type);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="CSS\style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camermood</title>
</head>

<body>

    <header>
        <video autoplay muted loop class="background-video">
            <source src="video\video_touristique.mp4" type="video/mp4">
            
        </video>
    
        <div class="header-content">
            <img class="onglet-image-logo" src="images\Logo_Camermood.png" alt="Logo CamerMood">
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

    <main>
        <h1>Sites Touristiques</h1>
        <div class="grille-destinations">
            <?php foreach ($destinationsFiltrees as $destination) : ?>
                <div class="carte-destination">
                    <img src="<?= htmlspecialchars($destination['image']) ?>" alt="<?= htmlspecialchars($destination['nom']) ?>">
                    <h3><?= htmlspecialchars($destination['nom']) ?></h3>
                    <p><?= htmlspecialchars($destination['ville']) ?>, <?= htmlspecialchars($destination['pays']) ?></p>
                    <p><?= htmlspecialchars($destination['note']) ?> - <?= htmlspecialchars($destination['experiences']) ?></p>
                    <p>À partir de : <?= htmlspecialchars($destination['prix']) ?></p>
                    <a href="description.php?id=<?= $destination['id'] ?>" class="bouton-voyager">Voyager</a>
                </div>
            <?php endforeach; ?>
        </div>
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
