<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'vendeur') {
    header('Location: connexion.php');
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

    // Vérifier paiement et abonnement
    $stmt = $pdo->prepare("SELECT paiement_effectue, abonnement_actif FROM utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$_SESSION['id_utilisateur']]);
    $result = $stmt->fetch();

    if (!$result || $result['paiement_effectue'] != 1 || $result['abonnement_actif'] != 1) {
        header('Location: abonnement.php');
        exit();
    }

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
