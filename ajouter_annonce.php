<?php
session_start();

// Vérification de la session admin
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit;
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'agropastoral';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $message = $_POST['message'];

    // Insertion de l'annonce dans la base de données
    $sql = "INSERT INTO notifications (titre, message) VALUES (:titre, :message)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['titre' => $titre, 'message' => $message]);

    // Redirection vers le tableau de bord après l'ajout
    header("Location: admin_dashboard.php");
    exit;
}
?>
