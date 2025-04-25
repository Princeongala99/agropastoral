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
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les notifications non lues
$stmt_notifications = $pdo->prepare("
    SELECT titre, message 
    FROM notifications 
    WHERE id = :role AND active = 1 
    ORDER BY date_creation DESC
");
$stmt_notifications->execute(['role' => $_SESSION['role']]);

$notifications = $stmt_notifications->fetchAll(PDO::FETCH_ASSOC);

// Retourner les notifications sous forme de JSON
echo json_encode($notifications);
?>
