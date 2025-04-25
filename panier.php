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
        :root {
            --agro-primary: #2E8B57;
            --agro-secondary: #8FBC8F;
            --agro-light: #F0FFF0;
            --agro-dark: #006400;
        }
        
        body {
            background-color: var(--agro-light);
        }
        
        .navbar {
            background-color: var(--agro-primary) !important;
        }
        
        footer {
            background-color: var(--agro-primary) !important;
            color: white !important;
        }
        
        .card {
            border: 1px solid var(--agro-secondary);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-img-top {
            height: 180px;
            object-fit: cover;
            border-bottom: 1px solid var(--agro-secondary);
        }
        
        .btn-agro {
            background-color: var(--agro-primary);
            color: white;
        }
        
        .btn-agro:hover {
            background-color: var(--agro-dark);
            color: white;
        }
        
        .empty-cart-icon {
            font-size: 5rem;
            color: var(--agro-secondary);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-leaf me-2"></i>AgroPastoral
        </a>
        <a href="acheteur_dashboard.php" class="btn btn-outline-light">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>
</nav>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-agro-dark">
                <i class="fas fa-shopping-cart me-2"></i>Mon Panier
            </h2>
        </div>
    </div>

    <?php if (empty($_SESSION['panier'])): ?>
        <div class="text-center py-5">
            <div class="empty-cart-icon mb-3">
                <i class="fas fa-shopping-basket"></i>
            </div>
            <h3 class="text-muted mb-3">Votre panier est vide</h3>
            <a href="acheteur_dashboard.php" class="btn btn-agro btn-lg">
                <i class="fas fa-store me-1"></i> Continuer vos achats
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($groupes as $nom => $groupe): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($groupe['infos']['image']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($nom) ?>">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?= htmlspecialchars($nom) ?></h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Prix unitaire:</span>
                                <strong><?= htmlspecialchars($groupe['infos']['prix']) ?> Fc</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Quantité:</span>
                                <strong><?= $groupe['quantite'] ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Total:</span>
                                <strong class="text-success"><?= $groupe['total'] ?> Fc</strong>
                            </div>
                            <?php foreach ($groupe['keys'] as $key): ?>
                                <a href="panier.php?action=supprimer&id=<?= $key ?>" 
                                   class="btn btn-outline-danger btn-sm w-100 mb-2"
                                   onclick="return confirm('Supprimer cet article ?')">
                                    <i class="fas fa-trash-alt me-1"></i> Supprimer
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row mt-5">
            <div class="col-md-6 offset-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Récapitulatif</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Total:</h5>
                            <h4 class="text-success fw-bold"><?= $total ?> Fc</h4>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="gestion_commandes.php" class="btn btn-agro btn-lg">
                                <i class="fas fa-credit-card me-2"></i> Passer la commande
                            </a>
                            <a href="acheteur_dashboard.php" class="btn btn-outline-success">
                                <i class="fas fa-store me-1"></i> Continuer mes achats
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer class="text-center py-3 mt-5">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> AgroPastoral - Tous droits réservés</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>