<?php
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    die("Vous devez être connecté pour ajouter un produit au panier.");
}

$id_acheteur = $_SESSION['id_utilisateur'];

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

// Vérifier si un produit est sélectionné
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = (int) $_GET['id'];

    // Récupérer les détails du produit + id du propriétaire
    $stmt = $pdo->prepare("SELECT id_produit, nom, description, prix, quantite, image, id_utilisateur FROM produits WHERE id_produit = :id");
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo '<div class="alert alert-danger">Produit introuvable !</div>';
        exit();
    }

    // Ajouter dans la table panier
    if (isset($_POST['ajouter_panier'])) {
        $quantite = (int) ($_POST['quantite'] ?? 1);

        if ($quantite < 1 || $quantite > $product['quantite']) {
            echo '<div class="alert alert-warning">Quantité invalide.</div>';
        } else {
            $date_ajout = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("INSERT INTO panier (id_acheteur, id_produit, quantite, date_ajout, nom_produit) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_acheteur, $product['id_produit'], $quantite, $date_ajout, $product['nom']]);

            header('Location: panier.php');
            exit();
        }
    }
} else {
    echo '<div class="alert alert-danger">ID de produit invalide !</div>';
    exit();
}
?>


<!-- HTML page -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du produit</title>
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
        <!-- Image de profil dans la navbar -->
        <li class="nav-item">
            <a href="modifier_profil.php" class="profile-link">
                <?php if ($_SESSION['photo']): ?>
                    <img src="<?= htmlspecialchars($_SESSION['photo']) ?>" alt="Photo de profil" class="profile-image" width="40">
                <?php else: ?>
                    <img src="default-icon.png" alt="Icône de profil par défaut" class="profile-image" width="40">
                <?php endif; ?>
            </a>
        </li>
    </div>
</nav>

<div class="container py-5">
    <div class="card p-4">
        <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid mb-3" style="max-height: 300px; object-fit: cover;">
        <h2><?= htmlspecialchars($product['nom']) ?></h2>
        <p><?= htmlspecialchars($product['description']) ?></p>
        <p><strong>Prix :</strong> <?= $product['prix'] ?> FC</p>
        <p><strong>Stock :</strong> <?= $product['quantite'] ?> unités</p>

        <form method="POST">
            <div class="mb-3">
                <label>Quantité</label>
                <input type="number" name="quantite" class="form-control" min="1" max="<?= $product['quantite'] ?>" value="1" required>
            </div>
            <a href="acheteur_dashboard.php" class="btn btn-outline-secondary">Retour</a>
            <button type="submit" name="ajouter_panier" class="btn btn-agro">Ajouter au panier</button>
        </form>
    </div>
</div>

</body>
</html>

