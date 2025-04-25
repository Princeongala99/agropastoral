<?php
session_start();

// Vérification de la session admin
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit;
}

// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=agropastoral;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupération des produits
$stmt = $pdo->query("SELECT * FROM produits ORDER BY id_produit DESC");
$produits = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Produits - AgroPastoral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="admin_dashboard.php">Retour au Dashboard</a>
    </div>
</nav>

<div class="container mt-4">
    <h3 class="mb-3">Gestion des Produits</h3>

    <a href="ajouter_produit.php" class="btn btn-success mb-3">Ajouter un produit</a>

    <table class="table table-bordered">
        <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Description</th> <!-- Changement ici -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit): ?>
            <tr>
                <td><?= $produit['id_produit'] ?></td>
                <td><?= htmlspecialchars($produit['nom']) ?></td>
                <td><?= number_format($produit['prix'], 2) ?> FCFA</td>
                <td><?= $produit['quantite'] ?></td>
                <td><?= htmlspecialchars($produit['description']) ?></td> <!-- Changement ici -->
                <td>
                    <a href="modifier_produit.php?id=<?= $produit['id_produit'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                    <a href="supprimer_produit.php?id=<?= $produit['id_produit'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
