<?php
session_start();

// Vérification de la connexion et du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'vendeur') {
    header("Location: connexion.php");
    exit();
}

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "agropast");
if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

// Traitement du formulaire d'ajout de produit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $quantite = $_POST['quantite'];
    $vendeur_id = $_SESSION['user_id'];

    // Gestion de l'upload de l'image
    $targetDir = "uploads/";
    $imageName = basename($_FILES['image']['name']);
    $targetFile = $targetDir . time() . "_" . $imageName;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $sql = "INSERT INTO produits (nom, description, image, prix, quantite, vendeur_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdii", $nom, $description, $targetFile, $prix, $quantite, $vendeur_id);
        
        if ($stmt->execute()) {
            $success = "Produit ajouté avec succès!";
        } else {
            $error = "Erreur lors de l'ajout du produit.";
        }
        $stmt->close();
    } else {
        $error = "Erreur lors du téléchargement de l'image.";
    }
}

// Récupération des produits du vendeur
$sql = "SELECT * FROM produits WHERE vendeur_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$produits = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Récupération des statistiques du vendeur
$sql_stats = "SELECT 
                COUNT(*) as total_produits,
                SUM(quantite) as total_stock,
                SUM(prix * quantite) as valeur_stock
              FROM produits 
              WHERE vendeur_id = ?";
$stmt_stats = $conn->prepare($sql_stats);
$stmt_stats->bind_param("i", $_SESSION['user_id']);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();
$stmt_stats->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Vendeur - AgroPastoral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #218838;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background-color: var(--primary-color);
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }
        
        .sidebar a {
            color: white;
            padding: 10px 15px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar a:hover {
            background-color: var(--secondary-color);
            border-radius: 5px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .card-stat {
            border-radius: 10px;
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .card-stat:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2rem;
            opacity: 0.7;
        }
        
        .product-img {
            height: 150px;
            object-fit: cover;
        }
        
        .btn-add-product {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-add-product:hover {
            background-color: var(--secondary-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-none d-md-block" style="width: 250px;">
            <div class="text-center mb-4">
                <h4>AgroPastoral</h4>
                <p>Espace Vendeur</p>
                <hr>
            </div>
            
            <a href="vendeur_dashboard.php" class="active">
                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
            </a>
            <a href="produits.php">
                <i class="fas fa-boxes me-2"></i>Mes Produits
            </a>
            <a href="ajoutproduit.php">
                <i class="fas fa-plus-circle me-2"></i>Ajouter Produit
            </a>
            <a href="commandes.php">
                <i class="fas fa-shopping-cart me-2"></i>Commandes
            </a>
            <a href="profil.php">
                <i class="fas fa-user me-2"></i>Mon Profil
            </a>
            <a href="deconnexion.php" class="mt-4">
                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
            </a>
            
            <div class="mt-5 px-3">
                <div class="card bg-light text-dark p-3">
                    <small>Connecté en tant que:</small>
                    <strong><?php echo htmlspecialchars($_SESSION['user_nom']); ?></strong>
                    <small class="text-muted">Vendeur</small>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-tachometer-alt me-2"></i> Tableau de bord</h2>
                <a href="ajoutproduit.php" class="btn btn-add-product">
                    <i class="fas fa-plus me-1"></i> Nouveau Produit
                </a>
            </div>
            
            <!-- Messages d'alerte -->
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card card-stat bg-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Produits</h6>
                                    <h3><?php echo $stats['total_produits']; ?></h3>
                                </div>
                                <div class="stat-icon text-success">
                                    <i class="fas fa-box-open"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card card-stat bg-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Stock Total</h6>
                                    <h3><?php echo $stats['total_stock']; ?></h3>
                                </div>
                                <div class="stat-icon text-info">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card card-stat bg-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Valeur Stock</h6>
                                    <h3><?php echo number_format($stats['valeur_stock'], 2); ?> €</h3>
                                </div>
                                <div class="stat-icon text-warning">
                                    <i class="fas fa-euro-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Derniers Produits -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-boxes me-2"></i> Mes Derniers Produits</h5>
                </div>
                <div class="card-body">
                    <?php if(count($produits) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Prix</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(array_slice($produits, 0, 5) as $produit): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>" class="product-img" style="width: 80px;">
                                            </td>
                                            <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                                            <td><?php echo substr(htmlspecialchars($produit['description']), 0, 50) . '...'; ?></td>
                                            <td><?php echo number_format($produit['prix'], 2); ?> €</td>
                                            <td><?php echo $produit['quantite']; ?></td>
                                            <td>
                                                <a href="modifier_produit.php?id=<?php echo $produit['id_produit']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="supprimer_produit.php?id=<?php echo $produit['id_produit']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="produits.php" class="btn btn-success">Voir tous mes produits</a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Vous n'avez aucun produit enregistré. <a href="ajoutproduit.php" class="alert-link">Ajoutez votre premier produit</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Formulaire rapide d'ajout de produit -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Ajouter un Produit</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom du produit</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prix" class="form-label">Prix (€)</label>
                                <input type="number" step="0.01" class="form-control" id="prix" name="prix" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantite" class="form-label">Quantité en stock</label>
                                <input type="number" class="form-control" id="quantite" name="quantite" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="image" class="form-label">Image du produit</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" name="ajouter" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour activer les tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>