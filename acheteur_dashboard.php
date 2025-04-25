<?php
session_start();
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
        . $_SESSION['message'] . 
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['message']); // Effacer le message après affichage
}

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

// Vérifier l'abonnement de l'utilisateur
$stmt = $pdo->prepare("SELECT role, abonnement_actif, nom, photo FROM utilisateur WHERE id_utilisateur = :id");
$stmt->execute(['id' => $_SESSION['id_utilisateur']]);
$user = $stmt->fetch();

if (!$user) {
    die("Utilisateur introuvable.");
}

// Enregistrer le rôle et l'abonnement en session
$_SESSION['role'] = $user['role'];
$_SESSION['abonnement_actif'] = $user['abonnement_actif'];
$_SESSION['nom'] = $user['nom'];
$_SESSION['photo'] = $user['photo'];

// Si l'utilisateur a un abonnement actif, passer le rôle en "vendeur"
if ($_SESSION['role'] === 'acheteur' && $_SESSION['abonnement_actif'] == 1) {
    $_SESSION['role'] = 'vendeur';

    // Mettre à jour le rôle en base
    $update = $pdo->prepare("UPDATE utilisateur SET role = 'vendeur' WHERE id_utilisateur = :id");
    $update->execute(['id' => $_SESSION['id_utilisateur']]);
}

// Charger les notifications
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
    <title>Espace Acheteur - AgroPastoral</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=1350&q=80');
            color: white;
            padding: 100px 20px;
        }
        .dashboard-btn {
            min-width: 250px;
        }

        /* Styles pour l'image de profil et l'effet de survol */
        .profile-link {
            display: inline-block;
            position: relative;
            transition: transform 0.3s ease;
        }

        .profile-image {
            transition: transform 0.3s ease, border 0.3s ease;
            border-radius: 50%; /* Assure-toi que l'image reste circulaire */
        }

        /* Effet de survol sur l'image */
        .profile-link:hover .profile-image {
            transform: scale(1.1); /* Agrandir l'image légèrement */
            border: 3px solid #28a745; /* Bordure verte lors du survol */
        }

        /* Affichage du message modifier profil */
        .profile-link:hover::after {
            content: 'Modifier Profil';
            position: absolute;
            top: 110%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            opacity: 0.8;
            white-space: nowrap;
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
                    <a class="nav-link dropdown-toggle" href="#" id="navbarNotifications" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger" id="notificationBadge"><?= $unread_notifications_count ?></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarNotifications" id="notificationMenu">
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
                <!-- Image de profil dans la navbar -->
                <li class="nav-item">
                    <a href="modifier_profil.php" class="profile-link">
                        <?php if ($_SESSION['photo']): ?>
                            <img src="<?= htmlspecialchars($_SESSION['photo']) ?>" alt="Photo de profil" class="profile-image" width="40">
                        <?php else: ?>
                            <img src="default-icon.png" alt="Icône de profil par défaut" class="profile-image" width="40">
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-5">Bienvenue <?php echo htmlspecialchars($_SESSION['nom']); ?> 🛒</h1>
        <p class="lead">Explorez nos produits et suivez vos commandes facilement</p>
        <div class="d-flex justify-content-center gap-4 flex-wrap mt-4">
            <a href="parcourir_produits.php" class="btn btn-light dashboard-btn">
                <i class="fas fa-seedling me-2"></i>Parcourir les produits
            </a>
            <?php if ($_SESSION['abonnement_actif'] == 0): ?>
                <a href="abonnement.php" class="btn btn-warning dashboard-btn">
                    <i class="fas fa-user-plus me-2"></i> Devenir Vendeur
                </a>
            <?php else: ?>
                <a href="vendeur_dashboard.php" class="btn btn-primary dashboard-btn">
                    <i class="fas fa-store me-2"></i> Espace Vendeur
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer class="text-center bg-light py-3 mt-5">
    <p>&copy; <?php echo date('Y'); ?> AgroPastoral - Espace Acheteur</p>
</footer>
<script>
    // Disparition automatique après 5 secondes
    setTimeout(function () {
        var alertElement = document.querySelector('.alert');
        if (alertElement) {
            alertElement.classList.remove('show');
            alertElement.classList.add('fade');
        }
    }, 5000); // 5000 millisecondes = 5 secondes
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
