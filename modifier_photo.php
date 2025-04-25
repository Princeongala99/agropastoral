<?php
session_start();

// Vérification de la session utilisateur
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

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

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo'])) {
    // Récupérer l'utilisateur actuel
    $stmt = $pdo->prepare("SELECT photo FROM utilisateur WHERE id_utilisateur = :id");
    $stmt->execute(['id' => $_SESSION['id_utilisateur']]);
    $user = $stmt->fetch();

    if ($user) {
        // Vérification du fichier
        $file = $_FILES['photo'];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 2 * 1024 * 1024; // 2 Mo max

        // Récupérer l'extension du fichier
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $error = "Seuls les fichiers JPG, JPEG, PNG, GIF sont autorisés.";
        } elseif ($file['size'] > $max_size) {
            $error = "Le fichier est trop volumineux. La taille maximale est de 2 Mo.";
        } else {
            // Déplacement du fichier vers le dossier de téléchargement
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Mise à jour du chemin de la photo dans la base de données
                $stmt = $pdo->prepare("UPDATE utilisateur SET photo = :photo WHERE id_utilisateur = :id");
                $stmt->execute([
                    'photo' => $file_path,
                    'id' => $_SESSION['id_utilisateur']
                ]);

                // Rediriger vers la page de profil avec un message de succès
                header('Location: modifier_profil.php?success=1');
                exit();
            } else {
                $error = "Une erreur est survenue lors du téléchargement de l'image.";
            }
        }
    } else {
        $error = "Utilisateur introuvable.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Photo - AgroPastoral</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=1350&q=80');
            color: white;
            padding: 100px 20px;
        }

        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .profile-image-container {
            text-align: center;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
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
                <li class="nav-item">
                    <a class="nav-link" href="Modifier_profil.php">Retour</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section text-center">
    <h1>Modifier votre Photo de Profil</h1>

    <div class="form-container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form action="modifier_photo.php" method="POST" enctype="multipart/form-data">
            <div class="profile-image-container">
                <img src="default-icon.png" alt="Icône par défaut" class="profile-image">
            </div>

            <div class="mb-3">
                <label for="photo" class="form-label">Choisissez une nouvelle photo</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Mettre à jour la photo</button>
        </form>
    </div>
</section>

<footer class="text-center mt-5">
  <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
