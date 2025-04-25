<?php
session_start();

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

// Vérifier si un produit a été sélectionné
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = $_GET['id'];

    // Récupérer les détails du produit
    $stmt = $pdo->prepare("SELECT id_produit, nom, description, prix, quantite, image FROM produits WHERE id_produit = :id");
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch();

    // Si le produit n'existe pas
    if (!$product) {
        echo '<div class="alert alert-danger" role="alert">Produit introuvable !</div>';
        exit();
    }

    // Ajouter au panier
    if (isset($_POST['ajouter_panier'])) {
        $quantite = $_POST['quantite'] ?? 1;

        // Vérifier si le produit est déjà dans le panier
        $found = false;
        foreach ($_SESSION['panier'] as $key => $item) {
            if ($item['id'] == $product['id']) {
                // Si le produit existe déjà, on met à jour la quantité
                $_SESSION['panier'][$key]['quantite'] += $quantite;
                $found = true;
                break;
            }
        }

        // Si le produit n'est pas dans le panier, on l'ajoute
        if (!$found) {
            $_SESSION['panier'][] = [
                'id' => $product['id'],
                'nom' => $product['nom'],
                'prix' => $product['prix'],
                'quantite' => $quantite,
                'image' => $product['image']
            ];
        }

        header('Location: panier.php');
        exit();
    }
} else {
    echo '<div class="alert alert-danger" role="alert">ID de produit invalide !</div>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du produit - AgroPastoral</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">AgroPastoral</a>
    </div>
</nav>

<section style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="container py-4" style="max-width: 600px;">
        <h2><?php echo htmlspecialchars($product['nom']); ?></h2>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
        <p><strong>Prix:</strong> <?php echo htmlspecialchars($product['prix']); ?> EUR</p>
        <p><strong>Quantité disponible:</strong> <?php echo htmlspecialchars($product['quantite']); ?></p>
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>" class="img-fluid mb-3">

        <!-- Formulaire pour ajouter au panier -->
        <form method="POST">
            <div class="mb-3">
                <label for="quantite" class="form-label">Quantité</label>
                <input type="number" name="quantite" class="form-control" min="1" max="<?php echo $product['quantite']; ?>" value="1" required>
            </div>
            <button type="submit" name="ajouter_panier" class="btn btn-primary">Ajouter au panier</button>
        </form>
    </div>
</section>

<footer class="text-center bg-light py-3 mt-5">
    <p>&copy; <?php echo date('Y'); ?> AgroPastoral - Détails du produit</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
