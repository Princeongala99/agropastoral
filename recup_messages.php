<?php
session_start();

// Connexion à la base de données
$host = 'localhost';
$dbname = 'agropastoral';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([]);
    exit;
}

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode([]);
    exit;
}

$current_user = $_SESSION['id_utilisateur'];
$receiver_id = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;

if (!$receiver_id) {
    echo json_encode([]);
    exit;
}

// Sélectionner les messages entre l'utilisateur courant et le destinataire
$stmt = $pdo->prepare("SELECT * FROM messages 
    WHERE (sender_id = ? AND receiver_id = ?) 
       OR (sender_id = ? AND receiver_id = ?)
    ORDER BY date_envoi ASC");

$stmt->execute([$current_user, $receiver_id, $receiver_id, $current_user]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
