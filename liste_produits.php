<?php
$conn = new mysqli("localhost", "root", "", "agropastoral");

if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

// Suppression d'un produit
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM produits WHERE id_produit = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: nosproduits.php"); // Redirige vers la page des produits après suppression
    exit();
}

// Récupérer les produits depuis la base de données
$sql = "SELECT * FROM produits";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nos Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Styles existants */
        :root {
            --primary-color: #4a8c3a;
            --secondary-color: #3a6b2d;
            --button-color: #ffc107; /* Jaune pour les boutons */
            --button-hover-color: #e69100; /* Jaune plus foncé */
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
            margin-bottom: 50px;
        }

        .product-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-10px);
        }

        .product-img {
            height: 200px;
            width: 200px;
            align-self: center;
            object-fit: cover;
        }

        footer {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 40px 0 20px;
        }

        /* Nouvelle disposition des boutons */
        .btn-container {
            display: flex;
            gap: 10px;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .btn-custom {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-custom i {
            font-size: 1.2rem;
        }

        /* Couleurs des boutons */
        .btn-primary {
            background-color:rgb(203, 157, 6);
            border: none;
        }

        .btn-primary:hover {
            background-color: rgb(177, 174, 22);
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color:rgb(83, 90, 95);
        }

        .btn-info {
            background-color: #4a8c3a;
            border: none;
        }

        .btn-info:hover {
            background-color:rgb(43, 92, 30);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">AgroPastoral</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="nosproduits.php">Nos productions</a></li>
                <li class="nav-item"><a class="nav-link" href="ajoutproduit.php">Ajouter produit</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section">
    <div class="container">
        <h1 class="display-5">🌿 Nos Produits</h1>
        <p class="lead">Découvrez les produits cultivés avec passion et savoir-faire.</p>
    </div>
</section>

<div class="container py-4">
    <div class="row g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card product-card">
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top product-img" alt="<?php echo htmlspecialchars($row['nom']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['nom']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <!-- Conteneur des boutons avec icônes et nouvelle disposition -->
                        <div class="btn-container">
                            <a href="#" class="btn btn-primary btn-custom">
                                <i class="fas fa-cart-plus"></i> Ajouter au panier
                            </a>
                            <a href="#" class="btn btn-secondary btn-custom">
                                <i class="fas fa-phone"></i> Contacter
                            </a>
                            <a href="#" class="btn btn-info btn-custom">
                                <i class="fas fa-map-marker-alt"></i> Localiser
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<footer class="text-center">
    <div class="container">
        <p>&copy; <?php echo date("Y"); ?> AgroPastoral. Tous droits réservés.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php $conn->close(); ?>
