<?php

require_once __DIR__.'/../includes/db_functions.php';
require_once __DIR__.'/../includes/session_functions.php';


session_start();

checkAdminAccess(); // Bloque l'accès si non-admin

// Vérifier la session au début de chaque page protégée
verifierSession();


// Vérifier si l'utilisateur est connecté et admin
if (!isset($_SESSION['user_id']) || !isAdmin($_SESSION['user_id'])) {
    header('Location: ../connexion.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Récupérer les réservations
$reservations = getAllReservations();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Réservations</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
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

    <main>
        <h1>Liste des réservations</h1>
        
        <table class="reservations-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Lieu ID</th>
                    <th>Date arrivée</th>
                    <th>Date départ</th>
                    <th>Personnes</th>
                    <th>Statut</th>
                    <th>Date création</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?= htmlspecialchars($reservation['id']) ?></td>
                    <td><?= htmlspecialchars($reservation['utilisateur_nom']) ?></td>
                    <td><?= htmlspecialchars($reservation['email']) ?></td>
                    <td><?= htmlspecialchars($reservation['lieu_id']) ?></td>
                    <td><?= htmlspecialchars($reservation['date_arrivee']) ?></td>
                    <td><?= htmlspecialchars($reservation['date_depart']) ?></td>
                    <td><?= htmlspecialchars($reservation['nb_personnes']) ?></td>
                    <td><?= htmlspecialchars($reservation['statut']) ?></td>
                    <td><?= htmlspecialchars($reservation['date_creation']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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