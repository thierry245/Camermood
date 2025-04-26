<?php
require_once __DIR__.'/includes/db_functions.php';
require_once __DIR__.'/donnees_destinations.php';
require_once __DIR__.'/includes/session_functions.php';

session_start();
// Vérifier la session au début de chaque page protégée
verifierSession();

// Initialisation des variables
$erreurs = [];
$succes = false;
$erreur = '';

// Récupérer l'ID du lieu depuis l'URL
$lieu_id = $_GET['lieu_id'] ?? null;
if (!$lieu_id) {
    die("Aucun lieu spécifié");
}

// Trouver le lieu correspondant
$lieu = null;
foreach ($destinations as $d) {
    if ($d['id'] == $lieu_id) {
        $lieu = $d;
        break;
    }
}

if (!$lieu) {
    die("Lieu non trouvé");
}

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données
    $lieu_id = $_POST['lieu_id'] ?? $lieu_id;
    $date_arrivee = $_POST['date_arrivee'] ?? '';
    $date_depart = $_POST['date_depart'] ?? '';
    $nb_personnes = $_POST['nb_personnes'] ?? 1;
    $commentaires = $_POST['commentaires'] ?? '';

    // Validation
    if (empty($date_arrivee)) {
        $erreurs['date_arrivee'] = "La date d'arrivée est obligatoire";
    } elseif (strtotime($date_arrivee) < strtotime('today')) {
        $erreurs['date_arrivee'] = "La date d'arrivée ne peut pas être dans le passé";
    }

    if (empty($date_depart)) {
        $erreurs['date_depart'] = "La date de départ est obligatoire";
    } elseif (strtotime($date_depart) <= strtotime($date_arrivee)) {
        $erreurs['date_depart'] = "La date de départ doit être après la date d'arrivée";
    }

    if ($nb_personnes < 1 || $nb_personnes > 10) {
        $erreurs['nb_personnes'] = "Nombre de personnes invalide";
    }

    if (empty($erreurs)) {
        if (createReservation(
            $_SESSION['user_id'],
            $lieu_id,
            $date_arrivee,
            $date_depart,
            $nb_personnes,
            $commentaires
        )) {
            $succes = true;
        } else {
            $erreur = "Erreur lors de la création de la réservation";
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
    <title>Réservation - <?= htmlspecialchars($lieu['nom']) ?> | CamerMood</title>
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

    <main class="form-container">
        <h1>Réserver <?= htmlspecialchars($lieu['nom']) ?></h1>
        
        <?php if($succes): ?>
            <div class="alert success">
                Votre réservation a été confirmée !
                <a href="compte.php" class="btn-secondary">Voir mes réservations</a>
            </div>
        <?php else: ?>
            <div class="reservation-summary">
                <h3>Détails du lieu</h3>
                <p><strong>Lieu:</strong> <?= htmlspecialchars($lieu['nom']) ?></p>
                <p><strong>Localisation:</strong> <?= htmlspecialchars($lieu['ville']) ?>, <?= htmlspecialchars($lieu['pays']) ?></p>
                <p><strong>Prix:</strong> <?= htmlspecialchars($lieu['prix']) ?></p>
            </div>

            <form method="POST" class="reservation-form">
                <div class="form-group">
                    <label for="date_arrivee">Date d'arrivée</label>
                    <input type="date" id="date_arrivee" name="date_arrivee" required
                           min="<?= date('Y-m-d') ?>"
                           value="<?= htmlspecialchars($_POST['date_arrivee'] ?? '') ?>">
                    <?php if(isset($erreurs['date_arrivee'])): ?>
                        <span class="error"><?= $erreurs['date_arrivee'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="date_depart">Date de départ</label>
                    <input type="date" id="date_depart" name="date_depart" required
                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                           value="<?= htmlspecialchars($_POST['date_depart'] ?? '') ?>">
                    <?php if(isset($erreurs['date_depart'])): ?>
                        <span class="error"><?= $erreurs['date_depart'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="nb_personnes">Nombre de personnes</label>
                    <select id="nb_personnes" name="nb_personnes" required>
                        <?php for($i=1; $i<=10; $i++): ?>
                            <option value="<?= $i ?>" <?= ($_POST['nb_personnes'] ?? 1) == $i ? 'selected' : '' ?>>
                                <?= $i ?> personne<?= $i>1 ? 's' : '' ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <?php if(isset($erreurs['nb_personnes'])): ?>
                        <span class="error"><?= $erreurs['nb_personnes'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="commentaires">Demandes spéciales</label>
                    <textarea id="commentaires" name="commentaires" rows="4"><?= 
                        htmlspecialchars($_POST['commentaires'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Confirmer la réservation</button>
                    <a href="<?= htmlspecialchars($lieu['lien']) ?>" class="btn-secondary">Retour</a>
                </div>
            </form>
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

    <script>
        document.getElementById('date_arrivee').addEventListener('change', function() {
            const arrivee = new Date(this.value);
            const departField = document.getElementById('date_depart');
            
            if (this.value && departField.value) {
                const depart = new Date(departField.value);
                if (depart <= arrivee) {
                    departField.value = '';
                    alert("La date de départ doit être après la date d'arrivée");
                }
            }
            
            if (this.value) {
                const nextDay = new Date(arrivee);
                nextDay.setDate(nextDay.getDate() + 1);
                departField.min = nextDay.toISOString().split('T')[0];
            }
        });
    </script>
</body>
</html>