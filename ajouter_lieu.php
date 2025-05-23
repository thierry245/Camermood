<?php
require_once __DIR__.'/includes/db_functions.php';
require_once __DIR__.'/includes/session_functions.php';
// Vérification de session ET des droits admin
verifierSession();
checkAdminAccess(); // Bloque l'accès si non-admin

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un lieu - CamerMood</title>
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
        
        <h1>Formulaire ajouter un lieu</h1>

        <form class="auth-form" id="formulaire-ajout-lieu" action="soumettre_lieu.php" method="POST" enctype="multipart/form-data">

            
            
            <!-- Nom du lieu -->

                <div class="form-group">
                    <label for="nom-lieu">Nom du lieu :</label>
                    <input type="text" id="nom-lieu" name="nom-lieu" required>
                    <span id="nom-lieu-error" class="error-message"></span>
                </div>
            

            <!-- Adresse -->

                <div class="form-group">
                    <label for="adresse-lieu">Adresse :</label>
                    <input type="text" id="adresse-lieu" name="adresse-lieu" required>
                    <span id="adresse-lieu-error" class="error-message"></span>
                </div>
            
            <!-- Téléphone -->

                <div class="form-group">
                    <label for="telephone-lieu">Téléphone :</label>
                    <input type="tel" id="telephone-lieu" name="telephone-lieu" required>
                    <span id="telephone-lieu-error" class="error-message"></span>
                </div>

            <!-- Prix -->

                <div class="form-group">
                    <label for="Prix-lieu">Prix :</label>
                    <input type="text" id="Prix-lieu" name="Prix-lieu" required>
                    <span id="prix-lieu-error" class="error-message"></span>
                </div>

            

            <!-- Site web -->
             <div class="form-group">

                <label for="site-web-lieu">Site web :</label>
                <input type="url" id="site-web-lieu" name="site-web-lieu">

             </div>
            
            <!-- Type de lieu -->
            <div class="form-group">
                <label for="type-lieu">Type de lieu :</label>
                <select id="type-lieu" name="type-lieu" required>
                    <option value="">Sélectionnez un type</option>
                    <option value="site-touristique">Site touristique</option>
                    <option value="restaurant">Restaurant</option>
                    <option value="hotel">Hôtel</option>
                    <option value="festival">Festival</option>
                </select>
                <span id="type-lieu-error" class="error-message"></span>
            </div>
            

            <!-- Région -->
            <div class="form-group">
                <label for="region-lieu">Région :</label>
                <select id="region-lieu" name="region-lieu" required>
                    <option value="">Sélectionnez une région</option>
                    <option value="yaounde">Yaoundé</option>
                    <option value="douala">Douala</option>
                    <option value="bafoussam">Bafoussam</option>
                    <option value="limbe">Limbe</option>
                </select>
                <span id="region-lieu-error" class="error-message"></span>
            </div>
            
            <!-- Description -->
            <div class="form-group">
                <label for="description-lieu">Description :</label>
                <textarea id="description-lieu" name="description-lieu" rows="4" required></textarea>
                <span id="description-lieu-error" class="error-message"></span>
            </div>
            
            
            <!-- Image -->
            <div class="form-group">

                <label for="image-lieu">Image :</label>
                <input type="file" id="image-lieu" name="image-lieu" accept="image/*">

            </div>

            <!-- Champ dynamique  --> 
            <div id="dynamic-fields-container">
                <!-- Les champs spécifiques apparaîtront ici automatiquement -->
            </div>

            <!-- Bouton de soumission -->
            <div class="form-group">
                <button type="submit" class="btn-primary">Ajouter le lieu</button>
            </div>

            <!-- Bouton Annuler -->
            <div class="form-group" >
                <a href="index.php" class="btn-secondary">Annuler</a>
            </div>
            
        </form>
        <script src="JS/script.js"></script>
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