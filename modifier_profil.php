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

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT nom, email, telephone, adresse, photo FROM utilisateur WHERE id_utilisateur = :id");
$stmt->execute(['id' => $_SESSION['id_utilisateur']]);
$user = $stmt->fetch();

if (!$user) {
    die("Utilisateur introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil - AgroPastoral</title>

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

        .profile-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .profile-image-container {
            text-align: center;
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
                    <a class="nav-link" href="Acheteur_dashboard.php">Retour</a>
                </li>
                
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section text-center">
    <h1>Modifier votre Profil</h1>
    <div class="form-container">
        <!-- Formulaire de modification de profil -->
        <form action="traitement_modification.php" method="POST" enctype="multipart/form-data">
            <div class="profile-image-container">
                <?php if ($user['photo']): ?>
                    <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Photo de Profil" class="profile-image">
                <?php else: ?>
                    <img src="default-icon.png" alt="Icône par défaut" class="profile-image">
                <?php endif; ?>
                <a href="modifier_photo.php" class="btn btn-secondary mt-2">Modifier la photo</a>
            </div>

            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <textarea class="form-control" id="adresse" name="adresse" rows="3" required><?= htmlspecialchars($user['adresse']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de Passe</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Laissez vide si pas de changement">
            </div>

            <button type="submit" class="btn btn-primary w-100">Mettre à jour</button>
        </form>
    </div>
</section>

<footer class="text-center mt-5">
  <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
