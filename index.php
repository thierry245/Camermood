<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__.'/includes/db_functions.php';
require_once __DIR__.'/includes/session_functions.php';

// Ne pas rediriger si non connecté pour la page d'accueil
session_start();
$isLoggedIn = verifierSession(false); // false empêche la redirection

$userNom = $isLoggedIn && !empty($_SESSION['user']['nom']) ? $_SESSION['user']['nom'] : 'Utilisateur';
$isAdmin = $isLoggedIn && $_SESSION['user']['is_admin'];
$showAddButton = $isAdmin;

// Récupérer les réservations si demandé
$showReservations = false;
$reservations = [];

if ($isLoggedIn && isset($_GET['show_reservations'])) {
    $showReservations = true;
    
    if ($isAdmin) {
        $reservations = getAllReservationsWithUsers();
    } else {
        $reservations = getUserReservations($_SESSION['user']['id']);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="CSS/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
    <title>Camermood</title>
</head>

<body>
    <header>
        <video autoplay muted loop class="background-video">
            <source src="video\video_festivals.mp4" type="video/mp4">
            
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
        <h1>Bienvenue sur CamerMood <?= $isLoggedIn ? htmlspecialchars($userNom) : '' ?></h1>
            
        <?php if($isLoggedIn): ?>
            <div class="user-welcome">
                <p>Content de vous revoir, <?= htmlspecialchars($userNom) ?> !</p>
                <div class="user-actions">
                    <a href="?show_reservations=1" class="btn-primary">Mes réservations</a>
                    <?php if($showReservations): ?>
                        <a href="index.php" class="btn-secondary">Masquer les réservations</a>
                    <?php endif; ?>
                    <a href="deconnexion.php" class="btn-logout">Déconnexion</a>
                </div>
            </div>
        <?php else: ?>
            <div class="auth-options">
                <a href="connexion.php" class="btn-primary">Se connecter</a>
                <a href="inscription.php" class="btn-secondary">S'inscrire</a>
            </div>
        <?php endif; ?>

        <?php if ($showAddButton): ?>
            <a href="ajouter_lieu.php" class="bouton-ajouter-lieu">Ajouter un lieu</a>
        <?php endif; ?>

        <?php if ($showReservations): ?>
            <div class="reservations-container">
                <h2><?= $isAdmin ? 'Toutes les réservations' : 'Mes réservations' ?></h2>
                
                <?php if (empty($reservations)): ?>
                    <p class="no-reservations">Aucune réservation trouvée.</p>
                <?php else: ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <div class="reservation-card">
                            <?php if (!$isAdmin && isset($reservation['lieu_image'])): ?>
                                <img src="<?= htmlspecialchars($reservation['lieu_image']) ?>" 
                                     alt="<?= htmlspecialchars($reservation['lieu_nom']) ?>" 
                                     class="reservation-image">
                            <?php endif; ?>
                            
                            <div class="reservation-details">
                                <?php if ($isAdmin): ?>
                                    <div class="admin-user-info">
                                        <strong>Client:</strong> 
                                        <?= htmlspecialchars($reservation['user_nom']) ?> 
                                        (<?= htmlspecialchars($reservation['user_email']) ?>)
                                    </div>
                                <?php endif; ?>
                                
                                <h3><?= htmlspecialchars($reservation['lieu_nom']) ?></h3>
                                <p><strong>Type:</strong> <?= htmlspecialchars($reservation['lieu_type']) ?></p>
                                <p><strong>Dates:</strong> 
                                    Du <?= date('d/m/Y', strtotime($reservation['date_arrivee'])) ?> 
                                    au <?= date('d/m/Y', strtotime($reservation['date_depart'])) ?>
                                </p>
                                <p><strong>Personnes:</strong> <?= $reservation['nb_personnes'] ?></p>
                                <p><strong>Statut:</strong> 
                                    <span class="reservation-status status-<?= htmlspecialchars(strtolower($reservation['statut'])) ?>">
                                        <?=
                                        // Normalisation des statuts
                                        match (true) {
                                            (stripos($reservation['statut'], 'confirm')) !== false => 'Confirmée',
                                            (stripos($reservation['statut'], 'annul')) !== false   => 'Annulée',
                                            default => htmlspecialchars($reservation['statut'])
                                        }
                                        ?>
                                    </span>
                                </p>
                                
                                <div class="reservation-actions">
                                    <?php 
                                    $statut = $reservation['statut'];
                                    $isConfirmed = ($statut == 'confirmée' || $statut == 'confirmÃ©e' || stripos($statut, 'confirm') !== false);
                                    
                                    if ($isConfirmed): ?>
                                        <a href="annuler_reservation.php?id=<?= $reservation['id'] ?>" 
                                        class="btn-small btn-danger">Annuler</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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