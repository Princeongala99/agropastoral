<?php
session_start();

// Initialiser le panier s’il n’existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Supprimer un produit
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    foreach ($_SESSION['panier'] as $key => $item) {
        if ($item['id'] === $id) {
            unset($_SESSION['panier'][$key]);
            break;
        }
    }
    $_SESSION['panier'] = array_values($_SESSION['panier']); // Réindexer
    header('Location: panier.php');
    exit();
}

$total = 0;
foreach ($_SESSION['panier'] as $item) {
    $total += $item['prix'] * $item['quantite'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --agro-primary: #2E8B57;
            --agro-dark: #006400;
        }
        .btn-agro { background-color: var(--agro-primary); color: white; }
        .btn-agro:hover { background-color: var(--agro-dark); }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="index.php">AgroPastoral</a>
    </div>
</nav>

<div class="container py-5">
    <h2>Mon Panier</h2>

    <?php if (empty($_SESSION['panier'])): ?>
        <div class="alert alert-info">Votre panier est vide.</div>
        <a href="acheteur_dashboard.php" class="btn btn-agro">Retour à la boutique</a>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($_SESSION['panier'] as $item): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($item['nom']) ?></h5>
                            <p>Prix : <?= $item['prix'] ?> €</p>
                            <p>Quantité : <?= $item['quantite'] ?></p>
                            <p>Total : <strong><?= $item['prix'] * $item['quantite'] ?> €</strong></p>
                            <a href="panier.php?action=supprimer&id=<?= $item['id'] ?>" class="btn btn-danger w-100" onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-5 text-end">
            <h4>Total général : <?= $total ?> €</h4>
            <a href="gestion_commandes.php" class="btn btn-agro btn-lg mt-2">Passer la commande</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
