<?php
session_start();

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

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

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les commandes de l'utilisateur
$sql = "
SELECT c.*, pr.nom AS nom_produit, u.nom AS nom_vendeur
FROM commandes c
JOIN produits pr ON c.id_produit = pr.id_produit
JOIN utilisateur u ON pr.id_utilisateur = u.id_utilisateur
WHERE c.id_utilisateur = ?
ORDER BY c.date_commande DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_utilisateur]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">Mes Commandes</h2>

    <?php if (empty($commandes)): ?>
        <div class="alert alert-info">Vous n'avez passé aucune commande.</div>
        <a href="acheteur_dashboard.php" class="btn btn-success">Retour à la boutique</a>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Produit</th>
                    <th>Propriétaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Date de commande</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['nom_produit']) ?></td>
                        <td><?= htmlspecialchars($commande['nom_vendeur']) ?></td>
                        <td><?= $commande['quantite'] ?></td>
                        <td><?= $commande['total'] ?> FC</td>
                        <td><?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
