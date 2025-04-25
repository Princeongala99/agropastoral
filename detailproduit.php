<?php
// Récupérer l'ID du produit depuis l'URL
$id_produit = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$conn = new mysqli("localhost", "root", "", "agropast");
if ($conn->connect_error) die("Connexion échouée: " . $conn->connect_error);

// Requête principale du produit + vendeur (filtrée par rôle "vendeur")
$sql = "SELECT p.*, 
               u.nom AS vendeur, 
               u.photo AS photo_vendeur,
               u.id_utilisateur
        FROM produits p 
        JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur 
        WHERE p.id_produit = ? AND u.role = 'vendeur'";

$stmt = $conn->prepare($sql);
if (!$stmt) die("Erreur de préparation: " . $conn->error);

$stmt->bind_param("i", $id_produit);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h3 class='text-center text-danger mt-5'>Produit non trouvé ou vendeur invalide.</h3>";
    exit;
}

$produit = $result->fetch_assoc();

// Produits similaires (autres produits du même vendeur)
$sql_similaires = "SELECT p.* FROM produits p
                  JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur
                  WHERE p.id_produit != ? 
                  AND p.id_utilisateur = ?
                  AND u.role = 'vendeur'
                  LIMIT 4";

$stmt_s = $conn->prepare($sql_similaires);
$stmt_s->bind_param("ii", $id_produit, $produit['id_utilisateur']);
$stmt_s->execute();
$similaires = $stmt_s->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($produit['nom']) ?> - AgroPastoral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-gallery img {
            cursor: pointer;
            transition: transform 0.3s;
        }
        .product-gallery img:hover {
            transform: scale(1.03);
        }
        .seller-card {
            border-left: 3px solid #28a745;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <!-- Fil d'Ariane -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="accueil.php">Accueil</a></li>
                <li class="breadcrumb-item"><a href="nosproduits.php">Produits</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($produit['nom']) ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Galerie -->
            <div class="col-md-6">
                <div class="mb-4">
                    <img src="<?= htmlspecialchars($produit['image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($produit['nom']) ?>">
                </div>
                <div class="row product-gallery">
                    <div class="col-3"><img src="https://via.placeholder.com/100" class="img-thumbnail"></div>
                    <div class="col-3"><img src="https://via.placeholder.com/100" class="img-thumbnail"></div>
                    <div class="col-3"><img src="https://via.placeholder.com/100" class="img-thumbnail"></div>
                </div>
            </div>

            <!-- Détails produit -->
            <div class="col-md-6">
                <h1 class="mb-3"><?= htmlspecialchars($produit['nom']) ?></h1>

                <div class="d-flex align-items-center mb-3">
                    <span class="h3 text-success me-3"><?= number_format($produit['prix'], 2) ?> €</span>
                    <span class="badge bg-<?= $produit['quantite'] > 0 ? 'success' : 'danger' ?>">
                        <?= $produit['quantite'] > 0 ? 'En stock' : 'Rupture' ?>
                    </span>
                </div>

                <p class="lead mb-4"><?= htmlspecialchars($produit['description']) ?></p>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Caractéristiques</h5>
                        <ul>
                            <li><strong>Variété:</strong> <?= htmlspecialchars($produit['variete'] ?? 'N/A') ?></li>
                            <li><strong>Poids moyen:</strong> <?= htmlspecialchars($produit['poids'] ?? 'N/A') ?> kg</li>
                            <li><strong>Méthode de culture:</strong> <?= htmlspecialchars($produit['culture'] ?? 'N/A') ?></li>
                        </ul>
                    </div>
                </div>

                <!-- Formulaire ajout panier -->
                <form action="panier.php" method="POST" class="mb-4">
                    <input type="hidden" name="id_produit" value="<?= $produit['id_produit'] ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="quantite" class="form-label">Quantité</label>
                            <select class="form-select" id="quantite" name="quantite">
                                <?php for($i = 1; $i <= min($produit['quantite'], 10); $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-cart-plus me-2"></i>Ajouter au panier
                    </button>
                </form>

                <!-- Carte vendeur -->
                <div class="card seller-card">
                    <div class="card-body d-flex align-items-center">
                        <img src="<?= !empty($produit['photo_vendeur']) ? htmlspecialchars($produit['photo_vendeur']) : 'https://ui-avatars.com/api/?name='.urlencode($produit['vendeur']) ?>" 
                             class="rounded-circle me-3" width="60" height="60">
                        <div>
                            <h6 class="mb-1">Vendu par <strong><?= htmlspecialchars($produit['vendeur']) ?></strong></h6>
                            <div class="text-muted small">
                                <i class="fas fa-star text-warning"></i> 4.8 (126 avis)
                            </div>
                            <a href="vendeur.php?id=<?= $produit['id_utilisateur'] ?>" class="small text-success">Voir tous ses produits</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits similaires -->
        <?php if ($similaires->num_rows > 0): ?>
        <div class="mt-5">
            <h3 class="mb-4">Autres produits de ce vendeur</h3>
            <div class="row">
                <?php while($similaire = $similaires->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($similaire['image']) ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($similaire['nom']) ?></h5>
                            <p class="text-success"><?= number_format($similaire['prix'], 2) ?> €</p>
                            <a href="detailproduit.php?id=<?= $similaire['id_produit'] ?>" class="btn btn-outline-success btn-sm">Voir</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$stmt_s->close();
$conn->close();
?>
