<?php
session_start();
require_once __DIR__.'/includes/db_functions.php';

// Vérifier si l'ID est passé dans l'URL
if (!isset($_GET['id'])) {
    header('HTTP/1.0 400 Bad Request');
    die("ID de destination non spécifié.");
}

// Récupérer la destination depuis la base de données
$id = (int)$_GET['id']; // Conversion en entier pour la sécurité
$destination = getDestinationById($id);

if (!$destination) {
    header('HTTP/1.0 404 Not Found');
    die("Destination non trouvée.");
}

// Préparation des données pour l'affichage sécurisé
$nom = htmlspecialchars($destination['nom']);
$ville = htmlspecialchars($destination['ville']);
$pays = htmlspecialchars($destination['pays']);
$note = htmlspecialchars($destination['note']);
$experiences = htmlspecialchars($destination['experiences']);
$prix = htmlspecialchars($destination['prix']);
$image = htmlspecialchars($destination['image']);
$description = !empty($destination['description']) ? htmlspecialchars($destination['description']) : "Description à venir.";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="CSS/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nom ?> - CamerMood</title>
</head>
<body>

    <header>
        <video autoplay muted loop class="background-video">
            <source src="video/video_touristique.mp4" type="video/mp4">
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

    <main>
        <h1><?= $nom ?></h1>
        
        <?php if(isset($_SESSION['user']['nom'])): ?>
            <div class="user-welcome">
                <p>Connecté en tant que : <?= htmlspecialchars($_SESSION['user']['nom']) ?></p>
            </div>
        <?php endif; ?>

        <div class="description-destination">
            <img src="<?= $image ?>" alt="<?= $nom ?>">
            <p><strong>Ville :</strong> <?= $ville ?></p>
            <p><strong>Pays :</strong> <?= $pays ?></p>
            <p><strong>Note :</strong> <?= $note ?></p>
            <p><strong>Expériences vécues :</strong> <?= $experiences ?></p>
            <p><strong>Prix :</strong> <?= $prix ?></p>
            <p><strong>Description :</strong> <?= $description ?></p>
        </div>
        
        <div class="bouton-reserver">
            <?php if(isset($_SESSION['user']['id'])): ?>
                <a href="reservation.php?lieu_id=<?= $id ?>" class="btn-reserver">Réserver</a>
            <?php else: ?>
                <div class="auth-required">
                    <p>Vous devez être connecté pour réserver.</p>
                    <div class="auth-options">
                        <a href="connexion.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-primary">Se connecter</a>
                        <a href="inscription.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-secondary">S'inscrire</a>
                    </div>
                </div>
            <?php endif; ?>
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