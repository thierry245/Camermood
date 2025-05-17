<?php

require_once __DIR__.'/includes/db_functions.php';
require_once __DIR__.'/includes/session_functions.php';

session_start();
verifierSession();
checkAdminAccess();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupérer les données du formulaire avec filter_input
        $typeLieu = filter_input(INPUT_POST, 'type-lieu', FILTER_SANITIZE_STRING);
        $nomLieu = filter_input(INPUT_POST, 'nom-lieu', FILTER_SANITIZE_STRING);
        $descriptionLieu = filter_input(INPUT_POST, 'description-lieu', FILTER_SANITIZE_STRING);
        $adresseLieu = filter_input(INPUT_POST, 'adresse-lieu', FILTER_SANITIZE_STRING);
        $regionLieu = filter_input(INPUT_POST, 'region-lieu', FILTER_SANITIZE_STRING);
        $telephoneLieu = filter_input(INPUT_POST, 'telephone-lieu', FILTER_SANITIZE_STRING);
        $prixLieu = filter_input(INPUT_POST, 'Prix-lieu', FILTER_SANITIZE_STRING);
        $siteWebLieu = filter_input(INPUT_POST, 'site-web-lieu', FILTER_SANITIZE_URL);

        // Validation des données
        if (empty($nomLieu) || empty($descriptionLieu)) {
            throw new Exception("Nom et description sont obligatoires");
        }

        // Traitement de l'image
        $cheminUpload = null;
        if (isset($_FILES['image-lieu']) && $_FILES['image-lieu']['error'] === UPLOAD_ERR_OK) {
            $cheminTemporaireImage = $_FILES['image-lieu']['tmp_name'];
            $nomImage = basename($_FILES['image-lieu']['name']);
            $cheminUpload = 'telechargement/' . $nomImage;
            
            if (!move_uploaded_file($cheminTemporaireImage, $cheminUpload)) {
                throw new Exception("Erreur lors du téléchargement de l'image");
            }
        }

        // Insertion dans la base de données
        $pdo = getDbWrite();
        $stmt = $pdo->prepare("
            INSERT INTO destinations 
            (type, nom, description, adresse, region, telephone, prix, site_web, image) 
            VALUES (:type, :nom, :description, :adresse, :region, :telephone, :prix, :site_web, :image)
        ");
        
        $stmt->bindValue(':type', $typeLieu, PDO::PARAM_STR);
        $stmt->bindValue(':nom', $nomLieu, PDO::PARAM_STR);
        $stmt->bindValue(':description', $descriptionLieu, PDO::PARAM_STR);
        $stmt->bindValue(':adresse', $adresseLieu, PDO::PARAM_STR);
        $stmt->bindValue(':region', $regionLieu, PDO::PARAM_STR);
        $stmt->bindValue(':telephone', $telephoneLieu, PDO::PARAM_STR);
        $stmt->bindValue(':prix', $prixLieu, PDO::PARAM_STR);
        $stmt->bindValue(':site_web', $siteWebLieu, PDO::PARAM_STR);
        $stmt->bindValue(':image', $cheminUpload, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            header('Location: merci.html');
            exit;
        } else {
            throw new Exception("Erreur lors de l'insertion dans la base de données");
        }

    } catch (Exception $e) {
        error_log("Erreur dans soumettre_lieu.php: " . $e->getMessage());
        die("Une erreur est survenue: " . $e->getMessage());
    }
}
?>