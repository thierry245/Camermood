<?php
require_once __DIR__.'/includes/db_functions.php';
require_once __DIR__.'/includes/session_functions.php';

session_start();
verifierSession(); // Cette page nécessite une connexion

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$reservationId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$reservationId) {
    header('Location: index.php');
    exit;
}

try {
    $pdo = getDbRead();
    
    if ($_SESSION['user']['is_admin']) {
        $stmt = $pdo->prepare("
            SELECT r.*, d.*, u.nom as user_nom, u.email as user_email
            FROM reservations r
            JOIN destinations d ON r.lieu_id = d.id
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            WHERE r.id = :id
        ");
    } else {
        $stmt = $pdo->prepare("
            SELECT r.*, d.*
            FROM reservations r
            JOIN destinations d ON r.lieu_id = d.id
            WHERE r.id = :id AND r.utilisateur_id = :userId
        ");
        $stmt->bindValue(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);
    }
    
    $stmt->bindValue(':id', $reservationId, PDO::PARAM_INT);
    $stmt->execute();
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Erreur reservation_details: " . $e->getMessage());
    header('Location: index.php?error=internal');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="CSS/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la réservation - CamerMood</title>
</head>
<body>
    <header>
        <video autoplay muted loop class="background-video">
            <source src="video/video_festivals.mp4" type="video/mp4">
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
        <h1>Détails de la réservation #<?= $reservation['id'] ?></h1>
        
        <div class="reservation-details">
            <?php if ($_SESSION['user']['is_admin']): ?>
                <div class="admin-info">
                    <h3>Informations client</h3>
                    <p><strong>Nom:</strong> <?= htmlspecialchars($reservation['user_nom']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($reservation['user_email']) ?></p>
                </div>
            <?php endif; ?>
            
            <div class="lieu-info">
                <h3>Informations du lieu</h3>
                <?php if (!empty($reservation['image'])): ?>
                    <img src="<?= htmlspecialchars($reservation['image']) ?>" 
                         alt="<?= htmlspecialchars($reservation['nom']) ?>" 
                         class="reservation-image">
                <?php endif; ?>
                <p><strong>Nom:</strong> <?= htmlspecialchars($reservation['nom']) ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($reservation['type']) ?></p>
                <p><strong>Ville:</strong> <?= htmlspecialchars($reservation['ville']) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($reservation['description']) ?></p>
            </div>
            
            <div class="reservation-info">
                <h3>Détails de la réservation</h3>
                <p><strong>Date d'arrivée:</strong> <?= date('d/m/Y', strtotime($reservation['date_arrivee'])) ?></p>
                <p><strong>Date de départ:</strong> <?= date('d/m/Y', strtotime($reservation['date_depart'])) ?></p>
                <p><strong>Nombre de personnes:</strong> <?= $reservation['nb_personnes'] ?></p>
                <p><strong>Statut:</strong> 
                    <span class="reservation-status status-<?= $reservation['statut'] ?>">
                        <?= $reservation['statut'] ?>
                    </span>
                </p>
                <p><strong>Date de création:</strong> <?= date('d/m/Y H:i', strtotime($reservation['date_creation'])) ?></p>
            </div>
            
            <div class="actions">
                <a href="index.php?show_reservations=1" class="btn-primary">Retour aux réservations</a>
                <?php if ($reservation['statut'] === 'confirmée'): ?>
                    <a href="annuler_reservation.php?id=<?= $reservation['id'] ?>" class="btn-danger">Annuler la réservation</a>
                <?php endif; ?>
            </div>
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