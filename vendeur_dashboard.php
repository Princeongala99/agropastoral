<?php
session_start();

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

    // Vérifier les infos de l'utilisateur
    $stmt = $pdo->prepare("SELECT role, paiement_effectue, abonnement_actif, nom, photo, date_abonnement FROM utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$_SESSION['id_utilisateur']]);
    $user = $stmt->fetch();

    if (!$user) {
        die("Utilisateur introuvable.");
    }

    $_SESSION['role'] = $user['role'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['abonnement_actif'] = $user['abonnement_actif'];
    $_SESSION['paiement_effectue'] = $user['paiement_effectue'];
    $_SESSION['photo'] = $user['photo'];  // Ajouter la photo dans la session

    $abonne = ($user['paiement_effectue'] == 1 && $user['abonnement_actif'] == 1);

    // Calcul des jours restants
    $jours_restants = null;
    if ($abonne && !empty($user['date_abonnement'])) {
        $date_abonnement = new DateTime($user['date_abonnement']);
        $date_expiration = clone $date_abonnement;
        $date_expiration->modify('+30 days');
        $aujourd_hui = new DateTime();
        $interval = $aujourd_hui->diff($date_expiration);

        if ($date_expiration >= $aujourd_hui) {
            $jours_restants = $interval->days;
        } else {
            $jours_restants = 0;
        }
    }

    // Notifications
    $stmt_notifications = $pdo->prepare("
        SELECT id, titre, message, date_creation, active 
        FROM notifications 
        WHERE role = :role AND active = 1 
        ORDER BY date_creation DESC
    ");
    $stmt_notifications->execute(['role' => $_SESSION['role']]);
    $notifications = $stmt_notifications->fetchAll();
    $unread_notifications_count = count($notifications);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - AgroPastoral</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1350&q=80');
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
        <a class="navbar-brand" href="#">AgroPastoral</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Chat et notifications restent accessibles même après déconnexion -->
                <li class="nav-item"><a class="nav-link" href="chat.php"><i class="fas fa-comments"></i> Chat</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarNotifications" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger" id="notificationBadge"><?= $unread_notifications_count ?></span>
                    </a>
                    <ul class="dropdown-menu" id="notificationMenu">
                        <?php if ($unread_notifications_count > 0): ?>
                            <?php foreach ($notifications as $notif): ?>
                                <li><a class="dropdown-item" href="#">
                                    <strong><?= htmlspecialchars($notif['titre']) ?>:</strong> <?= htmlspecialchars($notif['message']) ?>
                                </a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="#">Aucune notification</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="panier.php"><i class="fas fa-basket"></i> Panier</a></li>
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
                <li class="nav-item"><a class="nav-link" href="deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>


<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-5">Bienvenue <?= htmlspecialchars($_SESSION['nom']); ?> 👋</h1>
        <p class="lead">Votre espace personnel AgroPastoral</p>

        <?php if ($abonne && $jours_restants !== null): ?>
            <?php if ($jours_restants <= 5): ?>
                <div class="alert alert-danger w-100 mt-3">
                    ⚠️ Votre abonnement expire dans <strong><?= $jours_restants ?></strong> jour<?= $jours_restants > 1 ? 's' : '' ?>.
                    <a href="abonnement.php" class="btn btn-sm btn-light ms-2">Renouveler</a>
                </div>
            <?php else: ?>
                <div class="alert alert-success w-100 mt-3">
                    🎉 Abonnement actif. Il vous reste <strong><?= $jours_restants ?></strong> jour<?= $jours_restants > 1 ? 's' : '' ?> d’accès vendeur.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-center gap-4 flex-wrap mt-4">

            <!-- Fonctions acheteur toujours visibles -->
            <a href="parcourir_produits.php" class="btn btn-outline-light dashboard-btn">
                <i class="fas fa-search me-2"></i>Parcourir les produits
            </a>
            <a href="mes_commandes.php" class="btn btn-outline-light dashboard-btn">
                <i class="fas fa-box me-2"></i>Mes commandes
            </a>

            <!-- Fonctions vendeur si abonné -->
            <?php if ($abonne): ?>
                <a href="ajouter_produit.php" class="btn btn-light dashboard-btn">
                    <i class="fas fa-plus-circle me-2"></i>Ajouter un produit
                </a>
                <a href="gestion_commandes.php" class="btn btn-outline-light dashboard-btn">
                    <i class="fas fa-cogs me-2"></i>Gérer les commandes
                </a>
            <?php else: ?>
                <?php if ($_SESSION['paiement_effectue'] == 1): ?>
                    <div class="alert alert-info w-100 mt-3">
                        ⏳ Paiement reçu. Activation de l’abonnement en attente.
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning w-100 mt-3">
                        🔒 Fonctions vendeur accessibles après abonnement.
                        <a href="abonnement.php" class="btn btn-sm btn-success ms-3">S’abonner maintenant</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</section>

<footer class="text-center bg-light py-3 mt-5">
    <p>&copy; <?= date('Y'); ?> AgroPastoral - Tableau de bord</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateNotifications() {
        $.ajax({
            url: 'get_notifications.php',
            type: 'GET',
            success: function(data) {
                const notifications = JSON.parse(data);
                const unreadCount = notifications.length;
                $('#notificationBadge').text(unreadCount);
                const menu = $('#notificationMenu').empty();
                if (unreadCount > 0) {
                    notifications.forEach(n => {
                        menu.append(`<li><a class="dropdown-item" href="#"><strong>${n.titre}:</strong> ${n.message}</a></li>`);
                    });
                } else {
                    menu.append('<li><a class="dropdown-item" href="#">Aucune notification</a></li>');
                }
            }
        });
    }

    setInterval(updateNotifications, 10000);
    $(document).ready(updateNotifications);
</script>

</body>
</html>
