<?php
require_once __DIR__.'/includes/db_functions.php';
require_once __DIR__.'/donnees_destinations.php';

$pdo = getDbWrite();

try {
    $pdo->beginTransaction();
    
    // Préparation de la requête d'insertion
    $stmt = $pdo->prepare("
        INSERT INTO destinations 
        (type, nom, ville, pays, note, experiences, prix, image) 
        VALUES (:type, :nom, :ville, :pays, :note, :experiences, :prix, :image)
    ");
    
    // Insertion de chaque destination
    foreach ($destinations as $destination) {
        // Correction des IDs en double dans votre tableau actuel
        static $real_id = 1;
        $destination['id'] = $real_id++;
        
        $stmt->execute([
            ':type' => $destination['type'],
            ':nom' => $destination['nom'],
            ':ville' => $destination['ville'],
            ':pays' => $destination['pays'],
            ':note' => $destination['note'],
            ':experiences' => $destination['experiences'],
            ':prix' => $destination['prix'],
            ':image' => $destination['image']
        ]);
    }
    
    $pdo->commit();
    echo "Migration réussie ! " . count($destinations) . " destinations importées.";
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erreur lors de la migration : " . $e->getMessage());
}