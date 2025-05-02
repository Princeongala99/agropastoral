<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'agropastoral';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les positions des vendeurs
    $stmt = $pdo->prepare("SELECT nom, latitude, longitude, produit FROM postions WHERE latitude IS NOT NULL AND longitude IS NOT NULL");
    $stmt->execute();
    $vendeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourner les données au format JSON
    echo json_encode($vendeurs);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
