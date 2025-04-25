<?php
session_start();
if (!isset($_SESSION['id_utilisateur'])) {
    exit;
}

$sender_id = $_SESSION['id_utilisateur'];
$receiver_id = $_POST['receiver_id'];
$message = trim($_POST['message']);

if (empty($message)) exit;

$host = 'localhost';
$dbname = 'agropastoral';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, date_envoi) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$sender_id, $receiver_id, $message]);

} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>
