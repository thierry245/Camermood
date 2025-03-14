<?php
session_start(); // Démarrer la session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $typeLieu = $_POST['type-lieu'];
    $nomLieu = $_POST['nom-lieu'];
    $descriptionLieu = $_POST['description-lieu'];
    $adresseLieu = $_POST['adresse-lieu'];
    $regionLieu = $_POST['region-lieu'];
    $telephoneLieu = $_POST['telephone-lieu'];
    $prixLieu = $_POST['Prix-lieu'];
    $siteWebLieu = $_POST['site-web-lieu'];

    // Traitement de l'image (si nécessaire)
    if (isset($_FILES['image-lieu']) && $_FILES['image-lieu']['error'] === UPLOAD_ERR_OK) {
        $cheminTemporaireImage = $_FILES['image-lieu']['tmp_name'];
        $nomImage = basename($_FILES['image-lieu']['name']);
        $cheminUpload = 'telechargement/' . $nomImage;
        move_uploaded_file($cheminTemporaireImage, $cheminUpload);
    }

    // Créer un tableau pour le nouveau lieu
    $nouveauLieu = [
        'type' => $typeLieu,
        'nom' => $nomLieu,
        'description' => $descriptionLieu,
        'adresse' => $adresseLieu,
        'region' => $regionLieu,
        'telephone' => $telephoneLieu,
        'prix' => $prixLieu,
        'site_web' => $siteWebLieu,
        'image' => $cheminUpload ?? null,
    ];

    // Ajouter le nouveau lieu à la session
    if (!isset($_SESSION['lieux'])) {
        $_SESSION['lieux'] = []; // Initialiser le tableau si nécessaire
    }
    $_SESSION['lieux'][] = $nouveauLieu;

    // Afficher les données pour vérification (à des fins de débogage)
    echo "<pre>";
    print_r($_SESSION['lieux']);
    echo "</pre>";

    // Rediriger l'utilisateur après la soumission
    header('Location: merci.html');
    exit;
}
?>