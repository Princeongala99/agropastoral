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
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            
        }
        .modal-content {
    border-radius: 15px;
  }
  .modal-backdrop.show {
    opacity: 0.8;
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
            </li>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalLocalisation">
  Voir la carte des vendeurs
</button>

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
    <div class="text-end mb-3">
    <button class="btn btn-success" onclick="enregistrerPosition()">Enregistrer ma position</button>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Carte Leaflet
    var map = L.map('map').setView([51.505, -0.09], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    function enregistrerPosition() {
    if (!navigator.geolocation) {
        alert("La géolocalisation n'est pas supportée par ce navigateur.");
        return;
    }

    // Demander la position géographique de l'utilisateur
    navigator.geolocation.getCurrentPosition(function(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;

        // Afficher la position sur la carte
        L.marker([latitude, longitude]).addTo(map)
            .bindPopup("Vous êtes ici !")
            .openPopup();

        // Envoi de la position au serveur
        $.ajax({
            url: 'enregistrer_position.php',
            method: 'POST',
            data: {
                latitude: latitude,
                longitude: longitude
            },
            success: function(response) {
                console.log("Position enregistrée : " + response);
                alert("Votre position a été enregistrée avec succès.");
            },
            error: function(xhr, status, error) {
                console.error("Erreur d'enregistrement : " + error);
                alert("Une erreur s'est produite lors de l'enregistrement de la position.");
            }
        });
    }, function(error) {
        // Gestion des erreurs de géolocalisation
        alert("Impossible d'obtenir votre position : " + error.message);
    });
}


    // Récupération des vendeurs pour les afficher sur la carte
    $.getJSON('recuperer_vendeurs.php', function(vendeurs) {
        vendeurs.forEach(vendeur => {
            let produits = vendeur.produits.length ? vendeur.produits.join(", ") : "Aucun produit";
            let popupContent = `
                <strong>${vendeur.nom}</strong><br>
                Produits : ${produits}<br>
                <a href="chat.php?id=${vendeur.id}" class="btn btn-sm btn-success mt-1">Contacter</a>
            `;
            let marker = L.marker([vendeur.latitude, vendeur.longitude])
              .bindPopup(popupContent);
            markers.addLayer(marker);
        });
        map.addLayer(markers);
    });

    var markers = L.markerClusterGroup();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- MODALE DE LOCALISATION -->
<div class="modal fade" id="modalLocalisation" tabindex="-1" aria-labelledby="modalLocalisationLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalLocalisationLabel">Localisation des vendeurs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body p-0">
        <div id="map" style="height: 500px;"></div>
      </div>
    </div>
  </div>
</div>


</body>
</html>
