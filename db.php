<?php
// Paramètres de la base de données
$host = 'localhost';      // Remplace par ton hôte si nécessaire
$dbname = 'agropastoral'; // Remplace par le nom de ta base de données
$username = 'root';       // Ton nom d'utilisateur (par défaut "root" pour XAMPP)
$password = '';           // Ton mot de passe (par défaut vide pour XAMPP)

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configuration pour afficher les erreurs de PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
