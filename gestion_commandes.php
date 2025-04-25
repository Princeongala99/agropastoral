<?php
session_start();
require_once 'db.php'; // Inclusion du fichier de configuration pour la connexion à la base de données

// Debug : afficher les informations de session
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Vérification si le panier existe dans la session
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    echo "Votre panier est vide.";
    exit();
}

// Vérification si un utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Veuillez vous connecter pour passer commande.";
    exit();
}

// Récupération de l'ID de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Récupération des données du panier
$total = 0;
foreach ($_SESSION['panier'] as $key => $item) {
    $total += $item['prix'] * $item['quantite'];
}

// Démarrer une transaction pour garantir l'intégrité des données
$pdo->beginTransaction();

try {
    // 1. Création de la commande
    $sql_commande = "INSERT INTO commandes (user_id, total) VALUES (?, ?)";
    $stmt_commande = $pdo->prepare($sql_commande);
    $stmt_commande->execute([$user_id, $total]);
    
    // Récupérer l'ID de la commande nouvellement créée
    $commande_id = $pdo->lastInsertId();

    // 2. Insérer les articles dans `articles_commande`
    foreach ($_SESSION['panier'] as $key => $item) {
        // Vérifier que l'ID du produit existe
        $id_produit = $item['id'];

        if (isset($id_produit) && !empty($id_produit)) {
            // Insérer l'article dans `articles_commande`
            $sql_article = "INSERT INTO articles_commande (commande_id, id_produit, quantite, prix) VALUES (?, ?, ?, ?)";
            $stmt_article = $pdo->prepare($sql_article);
            $stmt_article->execute([$commande_id, $id_produit, $item['quantite'], $item['prix']]);
        }
    }

    // 3. Si tout est bien inséré, valider la transaction
    $pdo->commit();

    // Vider le panier après la commande
    unset($_SESSION['panier']);

    echo "Commande effectuée avec succès !";

} catch (Exception $e) {
    // En cas d'erreur, annuler la transaction
    $pdo->rollBack();
    echo "Erreur lors de la commande : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation Commande - AgroPastoral</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <h2 class="mb-4">🛒 Confirmation de la Commande</h2>

    <?php if (empty($_SESSION['panier'])): ?>
        <div class="alert alert-warning">Votre panier est vide.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($_SESSION['panier'] as $key => $item): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['nom']) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($item['nom']) ?></h5>
                            <p class="card-text mb-1">Prix unitaire : <strong><?= htmlspecialchars($item['prix']) ?> Fc</strong></p>
                            <p class="card-text mb-1">Quantité : <strong><?= $item['quantite'] ?></strong></p>
                            <p class="card-text text-muted">Total : <strong><?= $item['prix'] * $item['quantite'] ?> Fc</strong></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-end mt-4">
            <h4>Total à payer : <strong><?= $total ?> Fc</strong></h4>
            <form method="POST">
                <button type="submit" class="btn btn-success btn-lg mt-2"><i class="fas fa-credit-card"></i> Confirmer la commande</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<footer class="text-center bg-light py-3 mt-5">
    <p>&copy; <?= date('Y') ?> AgroPastoral - Commandes</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
