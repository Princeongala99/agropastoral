<?php
$conn = new mysqli("localhost", "root", "", "agropastoral");

if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

// Suppression d'un produit
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM produits WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: produits.php");
    exit();
}

// Modification d'un produit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $quantite = $_POST['quantite'];
    $prix = $_POST['prix'];
    $image = $_FILES['image']['name'];
    $tmpImage = $_FILES['image']['tmp_name'];

    if ($image) {
        $imagePath = 'uploads/' . time() . '_' . $image;
        move_uploaded_file($tmpImage, $imagePath);
        $sql = "UPDATE produits SET nom = ?, description = ?, quantite = ?, prix = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidsi", $nom, $description, $quantite, $prix, $imagePath, $id);
    } else {
        $sql = "UPDATE produits SET nom = ?, description = ?, quantite = ?, prix = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidi", $nom, $description, $quantite, $prix, $id);
    }

    $stmt->execute();
    header("Location: produits.php");
    exit();
}

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
    <style>
        :root {
            --primary-color: #4a8c3a;
            --secondary-color: #3a6b2d;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease-in-out;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }

        .product-img {
            height: 230px;
            width: 230px;
            align-self: center;
            
        }

        .card-body {
            padding: 1.2rem;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .card-text {
            font-size: 0.95rem;
            color: #444;
        }

        .card-text.fw-bold {
            font-size: 1rem;
            color: var(--primary-color);
        }

        footer {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 40px 0 20px;
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
                <li class="nav-item"><a class="nav-link" href="produits.php">Nos productions</a></li>
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
                        <p class="card-text fw-bold">Prix : <?php echo number_format($row['prix'], 0, ',', ' '); ?> FC</p>
                        <p class="card-text">Quantité disponible : <?php echo htmlspecialchars($row['quantite']); ?></p>
                        <a href="#" class="btn btn-warning"
                           data-bs-toggle="modal"
                           data-bs-target="#editProductModal"
                           data-id="<?php echo $row['id_produit']; ?>"
                           data-nom="<?php echo htmlspecialchars($row['nom']); ?>"
                           data-description="<?php echo htmlspecialchars($row['description']); ?>"
                           data-quantite="<?php echo $row['quantite']; ?>"
                           data-prix="<?php echo $row['prix']; ?>">
                           Modifier
                        </a>
                        <a href="?delete=<?php echo $row['id_produit']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">Supprimer</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Modal de modification -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Modifier le produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label for="editNom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="editNom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editQuantite" class="form-label">Quantité</label>
                        <input type="number" class="form-control" id="editQuantite" name="quantite" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPrix" class="form-label">Prix </label>
                        <input type="number" class="form-control" id="editPrix" name="prix" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="editImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="editImage" name="image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" name="edit" class="btn btn-primary">Sauvegarder</button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="text-center">
    <div class="container">
        <p>&copy; <?php echo date("Y"); ?> AgroPastoral. Tous droits réservés.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const editProductModal = document.getElementById('editProductModal');
    editProductModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        document.getElementById('editId').value = button.getAttribute('data-id');
        document.getElementById('editNom').value = button.getAttribute('data-nom');
        document.getElementById('editDescription').value = button.getAttribute('data-description');
        document.getElementById('editQuantite').value = button.getAttribute('data-quantite');
        document.getElementById('editPrix').value = button.getAttribute('data-prix');
    });
</script>

</body>
</html>

<?php $conn->close(); ?>