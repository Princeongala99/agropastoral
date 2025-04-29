<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php");
    exit;
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

// Récupération des données
$total_users = $pdo->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
$total_messages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$online_users = $pdo->query("SELECT COUNT(*) FROM utilisateur WHERE status = 'en ligne'")->fetchColumn();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $stmt = $pdo->prepare("SELECT id_utilisateur, nom, status FROM utilisateur WHERE nom LIKE :search OR status LIKE :search");
    $stmt->execute(['search' => '%' . $search . '%']);
} else {
    $stmt = $pdo->prepare("SELECT id_utilisateur, nom, status FROM utilisateur");
    $stmt->execute();
}

$utilisateurs = $stmt->fetchAll();

// Récupération des notifications actives
$stmt_notifications = $pdo->prepare("SELECT id, titre, message, date_creation, active FROM notifications ORDER BY date_creation DESC");
$stmt_notifications->execute();
$notifications = $stmt_notifications->fetchAll();

// Créer une nouvelle notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_notification'])) {
    $titre = $_POST['titre'];
    $message = $_POST['message'];
    
    $stmt = $pdo->prepare("INSERT INTO notifications (titre, message) VALUES (?, ?)");
    $stmt->execute([$titre, $message]);
    
    header("Location: admin_dashboard.php");
    exit();
}

// Activer/Désactiver une notification
if (isset($_GET['toggle_notification'])) {
    $id = $_GET['toggle_notification'];
    
    // Récupérer l'état actuel de la notification
    $stmt = $pdo->prepare("SELECT active FROM notifications WHERE id = ?");
    $stmt->execute([$id]);
    $notification = $stmt->fetch();
    
    // Inverser l'état
    $new_state = $notification['active'] == 1 ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE notifications SET active = ? WHERE id = ?");
    $stmt->execute([$new_state, $id]);
    
    header("Location: admin_dashboard.php");
    exit();
}

// Évolution des inscriptions par jour
$inscriptionsParJour = $pdo->query("
    SELECT DATE(date_inscription) as date_jour, COUNT(*) as total
    FROM utilisateur
    GROUP BY DATE(date_inscription)
    ORDER BY DATE(date_inscription) ASC
")->fetchAll(PDO::FETCH_ASSOC);

// convertir les données en JSON utilisable dans JavaScript
$dates = [];
$totaux = [];

foreach ($inscriptionsParJour as $row) {
    $dates[] = $row['date_jour'];
    $totaux[] = $row['total'];
}

$dates_json = json_encode($dates);
$totaux_json = json_encode($totaux);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - AgroPastoral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f3fdf2; }
        .dashboard-card { background-color: #fff; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
        .stat-card { text-align: center; }
        .user-status { font-weight: bold; color: #5cb85c; }
        .user-status.offline { color: #d9534f; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">AgroPastoral - Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="chat.php">Chat</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_utilisateurs.php">Gestion Utilisateurs</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion_produits.php">Gestion Produits</a></li>
                <li class="nav-item"><a class="nav-link" href="panneau_gestion.php">Gestion Abonnement</a></li>
                <li class="nav-item"><a class="nav-link" href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenu -->
<div class="container mt-4">

    <!-- Cartes statistiques -->
    <div class="row">
        <div class="col-md-4">
            <div class="dashboard-card stat-card">
                <h5>Utilisateurs inscrits</h5>
                <h2><?= $total_users ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card stat-card">
                <h5>Messages envoyés</h5>
                <h2><?= $total_messages ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card stat-card">
                <h5>Utilisateurs en ligne</h5>
                <h2><?= $online_users ?></h2>
            </div>
        </div>
    </div>
    
    <!-- Graphique des inscriptions -->
    <div class="dashboard-card mt-4">
        <h5>📈 Évolution des Inscriptions</h5>
        <canvas id="inscriptionsChart" height="80"></canvas>
    </div>

    <!-- Formulaire de création de notification -->
    <div class="dashboard-card mt-4">
        <h5>Créer une Nouvelle Notification</h5>
        <form method="POST">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" id="titre" name="titre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea id="message" name="message" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" name="create_notification" class="btn btn-success">Créer la Notification</button>
        </form>
    </div>

    <!-- Liste des notifications -->
    <div class="dashboard-card mt-4">
        <h5>Notifications</h5>
        <ul class="list-group">
            <?php foreach ($notifications as $notif): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($notif['titre']) ?>
                    <span class="badge bg-<?= $notif['active'] ? 'success' : 'danger' ?>"><?= $notif['active'] ? 'Active' : 'Inactive' ?></span>
                    <a href="?toggle_notification=<?= $notif['id'] ?>" class="btn btn-sm btn-outline-primary ms-2">
                        <?= $notif['active'] ? 'Désactiver' : 'Activer' ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Recherche utilisateurs -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Rechercher un utilisateur..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>

    <!-- Graphique + liste des utilisateurs -->
    <div class="row">
        <div class="col-md-6">
            <div class="dashboard-card">
                <h5>Répartition des Statuts</h5>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dashboard-card">
                <h5>Utilisateurs - Vue par statut</h5>
                <ul class="list-group">
                    <?php foreach ($utilisateurs as $u): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($u['nom']) ?>
                            <span class="user-status <?= $u['status'] === 'en ligne' ? '' : 'offline' ?>">
                                <?= htmlspecialchars($u['status']) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- Footer -->
<footer class="text-center mt-5 mb-4">
    <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['En ligne', 'Hors ligne'],
            datasets: [{
                label: 'Statut des utilisateurs',
                data: [<?= $online_users ?>, <?= $total_users - $online_users ?>],
                backgroundColor: ['#28a745', '#dc3545'],
                borderColor: ['#fff', '#fff'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
<script>
    const ctxInscriptions = document.getElementById('inscriptionsChart').getContext('2d');
    const inscriptionsChart = new Chart(ctxInscriptions, {
        type: 'line',
        data: {
            labels: <?= $dates_json ?>,
            datasets: [{
                label: 'Inscriptions par jour',
                data: <?= $totaux_json ?>,
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                borderColor: '#28a745',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { ticks: { autoSkip: true, maxTicksLimit: 10 } },
                y: { beginAtZero: true }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>
</body>
</html>
