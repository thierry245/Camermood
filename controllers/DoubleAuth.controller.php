<?php
require_once __DIR__.'/../includes/db_functions.php';

class DoubleAuth {
    private PDO $connexion;

    public function __construct() {
        $this->connexion = getDbWrite(); // Connexion en écriture
    }

    public function genererCode(int $userId, string $email): string {
        try {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiration = date('Y-m-d H:i:s', time() + 300); // 5 minutes

            error_log("Tentative d'UPDATE pour user $userId avec code $code");

            $stmt = $this->connexion->prepare("
                UPDATE utilisateurs 
                SET code_verification = ?, code_expiration = ? 
                WHERE id = ?
            ");
            $success = $stmt->execute([$code, $expiration, $userId]);

            if (!$success) {
                error_log("Échec d'exécution de la requête pour user $userId");
                throw new Exception("Échec UPDATE");
            }

            error_log("Requête UPDATE réussie pour user $userId");

            // Envoi du courriel
            $subject = "Votre code de vérification - CamerMood";
            $message = "Bonjour,\n\nVoici votre code de vérification : $code\n\nCe code expirera dans 5 minutes.\n\nCamerMood";
            $headers = "From: noreply@camermood.com\r\n";

            if (mail($email, $subject, $message, $headers)) {
                error_log("Code de vérification envoyé à $email");
            } else {
                error_log("Échec de l'envoi du code à $email");
            }

            return $code;

        } catch (PDOException $e) {
            error_log("Erreur dans genererCode: " . $e->getMessage());
            throw new Exception("Erreur interne");
        }
    }

    public function verifierCode(int $userId, string $code): bool {
        $stmt = $this->connexion->prepare("
            SELECT code_verification, code_expiration 
            FROM utilisateurs 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if (!$row) return false;

        $codeCorrect = $row['code_verification'] === $code;
        $nonExpire = strtotime($row['code_expiration']) > time();

        return $codeCorrect && $nonExpire;
    }
}
