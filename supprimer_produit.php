<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=agropastoral;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM produits WHERE id_produit = ?");
$stmt->execute([$id]);

header("Location: gestion_produits.php");
exit;
?>
