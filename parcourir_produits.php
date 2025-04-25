<?php
session_start();

// Vérifie que l'utilisateur est connecté et qu'il a un rôle valide
if (!isset($_SESSION['id_utilisateur']) || !in_array($_SESSION['role'], ['acheteur', 'vendeur'])) {
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

// Récupérer les produits
$stmt_products = $pdo->prepare("SELECT id_produit, nom, description, prix, quantite, image FROM produits ORDER BY nom ASC");
$stmt_products->execute();
$products = $stmt_products->fetchAll();

// Récupérer les notifications actives pour ce rôle
$stmt_notifications = $pdo->prepare("
    SELECT id, titre, message, date_creation, active 
    FROM notifications 
    WHERE role = :role AND active = 1 
    ORDER BY date_creation DESC
");
$stmt_notifications->execute(['role' => $_SESSION['role']]);
$notifications = $stmt_notifications->fetchAll();
$unread_notifications_count = count($notifications);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Parcourir les produits - AgroPastoral</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="style.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=1350&q=80');
            color: white;
            padding: 100px 20px;
        }
        .product-card {
            transition: transform 0.3s ease-in-out;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .product-img {
            object-fit: cover;
            height: 200px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">AgroPastoral</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="chat.php"><i class="fas fa-comments"></i> Chat</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarNotifications" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger" id="notificationBadge"><?= $unread_notifications_count ?></span>
                    </a>
                    <ul class="dropdown-menu" id="notificationMenu">
                        <?php if ($unread_notifications_count > 0): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <li><a class="dropdown-item" href="#">
                                    <strong><?= htmlspecialchars($notification['titre']) ?>:</strong>
                                    <?= htmlspecialchars($notification['message']) ?>
                                </a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="#">Aucune notification</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-5">Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?> 🛒</h1>
        <p class="lead">Explorez les produits disponibles et passez vos commandes facilement</p>
    </div>
</section>

<div class="container py-4">
    <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4">
                <div class="card product-card shadow-sm rounded">
                    <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($product['nom']) ?>">
                    <div class="card-body">
                        <h5 class="card-title text-truncate"><?= htmlspecialchars($product['nom']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars($product['description']) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary"><?= htmlspecialchars($product['prix']) ?> Fc</span>
                            <span class="badge bg-success"><?= htmlspecialchars($product['quantite']) ?> en stock</span>
                        </div>
                        <a href="details_produit.php?id=<?= $product['id_produit'] ?>" class="btn btn-warning mt-3 w-100">Savoir plus</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<footer class="text-center bg-light py-3 mt-5">
    <p>&copy; <?= date('Y') ?> AgroPastoral - Espace Acheteur</p>
</footer>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Notifications Dynamiques -->
<script>
    function updateNotifications() {
        $.ajax({
            url: 'get_notifications.php',
            type: 'GET',
            success: function(data) {
                const notifications = JSON.parse(data);
                const unreadCount = notifications.length;
                $('#notificationBadge').text(unreadCount);
                const notificationMenu = $('#notificationMenu');
                notificationMenu.empty();

                if (unreadCount > 0) {
                    notifications.forEach(notification => {
                        notificationMenu.append(`
                            <li><a class="dropdown-item" href="#">
                                <strong>${notification.titre}:</strong> ${notification.message}
                            </a></li>
                        `);
                    });
                } else {
                    notificationMenu.append('<li><a class="dropdown-item" href="#">Aucune notification</a></li>');
                }
            }
        });
    }

    setInterval(updateNotifications, 10000);
    $(document).ready(updateNotifications);
</script>

</body>
</html>
