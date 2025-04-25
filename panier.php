<?php
session_start();

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Gérer la suppression d'un article du panier
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
        header('Location: panier.php'); // Redirige pour éviter les suppressions multiples au refresh
        exit();
    }
}

// Regrouper les articles par nom de produit
$groupes = [];
$total = 0;
foreach ($_SESSION['panier'] as $key => $item) {
    $total += $item['prix'] * $item['quantite'];
    $nom = $item['nom'];
    if (!isset($groupes[$nom])) {
        $groupes[$nom] = [
            'infos' => $item,
            'quantite' => $item['quantite'],
            'total' => $item['prix'] * $item['quantite'],
            'keys' => [$key]
        ];
    } else {
        $groupes[$nom]['quantite'] += $item['quantite'];
        $groupes[$nom]['total'] += $item['prix'] * $item['quantite'];
        $groupes[$nom]['keys'][] = $key;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier - AgroPastoral</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-img-top {
            height: 180px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">AgroPastoral</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<div class="container py-4">
    <h2 class="mb-4">🛒 Mon Panier</h2>

    <?php if (empty($_SESSION['panier'])): ?>
        <div class="alert alert-warning">Votre panier est vide.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($groupes as $nom => $groupe): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($groupe['infos']['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($nom) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($nom) ?></h5>
                            <p class="card-text mb-1">Prix unitaire : <strong><?= htmlspecialchars($groupe['infos']['prix']) ?> Fc</strong></p>
                            <p class="card-text mb-1">Quantité : <strong><?= $groupe['quantite'] ?></strong></p>
                            <p class="card-text text-muted">Total : <strong><?= $groupe['total'] ?> Fc</strong></p>
                            <?php foreach ($groupe['keys'] as $key): ?>
                                <a href="panier.php?action=supprimer&id=<?= $key ?>" class="btn btn-outline-danger btn-sm mt-1"><i class="fas fa-trash-alt"></i> Supprimer</a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-end mt-4">
            <h4>Total à payer : <strong><?= $total ?> Fc</strong></h4>
            <a href="gestion_commandes.php" class="btn btn-success btn-lg mt-2"><i class="fas fa-credit-card"></i> Commander</a>
        </div>
    <?php endif; ?>
</div>

<footer class="text-center bg-light py-3 mt-5">
    <p>&copy; <?= date('Y') ?> AgroPastoral - Panier</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const deleteLinks = document.querySelectorAll('a[href*="action=supprimer"]');
        deleteLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                if (!confirm("Êtes-vous sûr de vouloir supprimer cet article du panier ?")) {
                    event.preventDefault();
                }
            });
        });
    });
</script>
</body>
</html>
