<?php
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    echo "Utilisateur non authentifié.";
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'agropastoral';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier que la latitude et la longitude sont bien envoyées
    if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $id_utilisateur = $_SESSION['id_utilisateur'];

        // Mettre à jour la table utilisateur
        $stmt = $pdo->prepare("UPDATE utilisateur SET latitude = :latitude, longitude = :longitude WHERE id_utilisateur = :id_utilisateur");
        $stmt->execute([
            ':latitude' => $latitude,
            ':longitude' => $longitude,
            ':id_utilisateur' => $id_utilisateur
        ]);

        // Vérifier si une entrée existe déjà dans la table posibles
        $check = $pdo->prepare("SELECT COUNT(*) FROM posibles WHERE id_utilisateur = :id");
        $check->execute([':id' => $id_utilisateur]);
        $exists = $check->fetchColumn();

        if ($exists > 0) {
            // Mise à jour si déjà existant
            $update = $pdo->prepare("UPDATE positions SET latitude = :latitude, longitude = :longitude WHERE id_utilisateur = :id");
            $update->execute([
                ':latitude' => $latitude,
                ':longitude' => $longitude,
                ':id' => $id_utilisateur
            ]);
        } else {
            // Insertion si nouveau
            $insert = $pdo->prepare("INSERT INTO positions (id_utilisateur, latitude, longitude) VALUES (:id, :latitude, :longitude)");
            $insert->execute([
                ':id' => $id_utilisateur,
                ':latitude' => $latitude,
                ':longitude' => $longitude
            ]);
        }

        echo "Position enregistrée avec succès.";
    } else {
        echo "Données manquantes.";
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
