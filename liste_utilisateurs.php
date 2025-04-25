<?php
session_start();

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

if (!isset($_SESSION['id_utilisateur'])) {
    echo "<p>Veuillez vous connecter d'abord.</p>";
    exit;
}

$current_user = $_SESSION['id_utilisateur'];

// Récupérer tous les utilisateurs sauf l'utilisateur connecté
$stmt = $pdo->prepare("SELECT id_utilisateur, nom, role FROM utilisateur WHERE id_utilisateur != ?");
$stmt->execute([$current_user]);
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Utilisateurs - AgroPastoral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h3 class="mb-4 text-success">Liste des utilisateurs</h3>
    <div class="list-group">
        <?php foreach ($utilisateurs as $utilisateur): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= htmlspecialchars($utilisateur['nom']) ?></strong> - <?= htmlspecialchars($utilisateur['role']) ?>
                </div>
                <a href="chat.php?receiver_id=<?= $utilisateur['id_utilisateur'] ?>" class="btn btn-success">Discuter</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
