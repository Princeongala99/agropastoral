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
    <style>
        :root {
            --agro-primary: #2E8B57;  /* Vert forêt */
            --agro-secondary: #8FBC8F; /* Vert clair */
            --agro-light: #F0FFF0;    /* Vert très clair */
            --agro-dark: #006400;     /* Vert foncé */
        }
        
        .bg-agro-primary {
            background-color: var(--agro-primary);
        }
        
        .bg-agro-secondary {
            background-color: var(--agro-secondary);
        }
        
        .bg-agro-light {
            background-color: var(--agro-light);
        }
        
        .btn-agro {
            background-color: var(--agro-primary);
            color: white;
            border: none;
        }
        
        .btn-agro:hover {
            background-color: var(--agro-dark);
            color: white;
        }
        
        .product-card {
            border: 1px solid var(--agro-secondary);
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-agro-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-agro-primary sticky-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-leaf me-2"></i>AgroPastoral
        </a>
    </div>
</nav>

<section class="py-5">
    <div class="container product-card bg-white p-4 shadow-sm" style="max-width: 600px;">
        <div class="text-center mb-4">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                 alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                 class="img-fluid rounded" 
                 style="max-height: 300px; object-fit: cover;">
        </div>
        
        <h2 class="text-agro-primary mb-3"><?php echo htmlspecialchars($product['nom']); ?></h2>
        
        <div class="mb-3">
            <h5 class="text-agro-dark">Description</h5>
            <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <h5 class="text-agro-dark">Prix</h5>
                <p class="fs-4 text-success fw-bold"><?php echo htmlspecialchars($product['prix']); ?> €</p>
            </div>
            <div class="col-md-6">
                <h5 class="text-agro-dark">Stock disponible</h5>
                <p class="fs-5"><?php echo htmlspecialchars($product['quantite']); ?> unités</p>
            </div>
        </div>

        <!-- Formulaire pour ajouter au panier -->
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="quantite" class="form-label fw-bold">Quantité</label>
                <input type="number" name="quantite" class="form-control" 
                       min="1" max="<?php echo $product['quantite']; ?>" 
                       value="1" required>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="acheteur_dashboard.php" class="btn btn-outline-secondary me-md-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
                <button type="submit" name="ajouter_panier" class="btn btn-agro">
                    <i class="fas fa-cart-plus me-1"></i> Ajouter au panier
                </button>
            </div>
        </form>
    </div>
</section>

<footer class="text-center text-white py-3 bg-agro-primary mt-5">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> AgroPastoral - Tous droits réservés</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>