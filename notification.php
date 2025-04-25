<?php
// notifications.php : Récupération des notifications actives
include('db.php');

$stmt = $pdo->prepare("SELECT titre, message, date_creation FROM notifications WHERE active = 1 ORDER BY date_creation DESC");
$stmt->execute();
$notifications = $stmt->fetchAll();
?>
