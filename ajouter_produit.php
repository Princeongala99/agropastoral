<?php
session_start();

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "agropastoral");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$success = "";
$error = "";

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION["id_utilisateur"])) {
    die("Utilisateur non connecté. Veuillez vous connecter pour ajouter un produit.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $description = $_POST["description"];
    $prix = $_POST["prix"];
    $stock = $_POST["stock"];
    $id_utilisateur = $_SESSION["id_utilisateur"];

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $targetDir = "images/";
        $imageName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . time() . "_" . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO produits (nom, description, prix, quantite, image, id_utilisateur) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdisi", $nom, $description, $prix, $stock, $targetFile, $id_utilisateur);

            if ($stmt->execute()) {
                $success = "Produit ajouté avec succès.";
            } else {
                $error = "Erreur lors de l'ajout à la base de données : " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Erreur lors du téléchargement de l'image.";
        }
    } else {
        $error = "Veuillez sélectionner une image valide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a8c3a;
            --secondary-color: #3a6b2d;
        }

        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 700px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .btn-agro {
            background-color: var(--primary-color);
            color: white;
            border-radius: 30px;
        }

        .btn-agro:hover {
            background-color: var(--secondary-color);
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
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="vendeur_dashboard.php">Retour</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Ajouter un produit</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="form-container">
    <h2 class="text-center mb-4">➕ Ajouter un nouveau produit</h2>

    <?php if ($success): ?>
        <div class="alert alert-success fade-alert"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger fade-alert"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du produit</label>
            <input type="text" name="nom" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="prix" class="form-label">Prix (en Francs Congolais)</label>
            <input type="number" name="prix" class="form-control" step="0.01" min="0" required>
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Stock disponible</label>
            <input type="number" name="stock" class="form-control" min="0" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image du produit</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-agro w-100">Ajouter le produit</button>
    </form>
</div>

<footer class="text-center">
    <div class="container">
        <p>&copy; <?php echo date("Y"); ?> AgroPastoral. Tous droits réservés.</p>
    </div>
</footer>

<script>
    setTimeout(() => {
        document.querySelectorAll('.fade-alert').forEach(el => {
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 1000);
        });
    }, 3000);
</script>

</body>
</html>

<?php $conn->close(); ?>
