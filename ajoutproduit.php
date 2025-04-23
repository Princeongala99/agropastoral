<?php
$conn = new mysqli("localhost", "root", "", "agropastoral");

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $description = $_POST["description"];
    $quantite = $_POST["quantite"]; 
    $prix = $_POST["prix"];         

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $targetDir = "images/";
        $imageName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . time() . "_" . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO produits (nom, description, quantite, prix, image) VALUES (?, ?, ?, ?, ?)"); // 👈 Modifié
            $stmt->bind_param("ssids", $nom, $description, $quantite, $prix, $targetFile); 

            if ($stmt->execute()) {
                $success = "Produit ajouté avec succès.";
            } else {
                $error = "Erreur lors de l'ajout à la base de données.";
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4a8c3a;
            --secondary-color: #3a6b2d;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Montserrat', sans-serif;
        }

        .form-container {
            max-width: 600px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .btn-agro {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
        }

        .btn-agro:hover {
            background-color: var(--secondary-color);
        }

        footer {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 40px 0 20px;
        }

        /* Loader */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loader-circle {
            border: 6px solid #d4edda;
            border-top: 6px solid var(--primary-color);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<!-- Loader -->
<div id="loader">
    <div class="loader-circle"></div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">AgroPastoral</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="produits.php">Nos produits</a></li>
                <li class="nav-item"><a class="nav-link active" href="ajoutproduit.php">Ajouter un produit</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="form-container">
    <h3 class="text-center mb-4">➕ Ajouter un nouveau produit</h3>

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
            <label for="quantite" class="form-label">Quantité</label>
            <input type="number" name="quantite" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label for="prix" class="form-label">Prix unitaire</label>
            <input type="number" name="prix" step="0.01" class="form-control" required>
        </div>


        <div class="mb-3">
            <label for="image" class="form-label">Image du produit</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-agro w-100">Ajouter le produit</button>
    </form>
</div>

<footer class="text-center">
    <div class="container">
        <p>&copy; <?php echo date("Y"); ?> AgroPastoral. Tous droits réservés.</p>
    </div>
</footer>

<!-- Scripts -->
<script>
    // Masquer le loader au chargement
    window.addEventListener("load", function () {
        const loader = document.getElementById("loader");
        loader.style.opacity = "0";
        setTimeout(() => loader.style.display = "none", 500);
    });

    // Disparition automatique des alertes
    setTimeout(() => {
        document.querySelectorAll('.fade-alert').forEach(el => {
            el.style.transition = "opacity 1s ease";
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 1000);
        });
    }, 3000);
</script>

</body>
</html>

<?php $conn->close(); ?>
