<?php
session_start();

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

// Sécurité : vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

$current_user = $_SESSION['id_utilisateur'];

// Fonctions pour rôle
function getRoleBadgeClass($role) {
    switch (strtolower($role)) {
        case 'admin': return 'bg-danger';
        case 'vendeur': return 'bg-success';
        case 'acheteur': return 'bg-primary';
        default: return 'bg-secondary';
    }
}

function getRoleIcon($role) {
    switch (strtolower($role)) {
        case 'admin': return '👑';
        case 'vendeur': return '📦';
        case 'acheteur': return '🛒';
        default: return '👤';
    }
}

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT abonnement_actif, paiement_effectue FROM utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$current_user]);
$user = $stmt->fetch();

$abonnement_en_attente = ($user['paiement_effectue'] && !$user['abonnement_actif']);

// Récupérer la liste des autres utilisateurs AVEC rôle
$stmt = $pdo->prepare("SELECT id_utilisateur, nom, role FROM utilisateur WHERE id_utilisateur != ?");
$stmt->execute([$current_user]);
$utilisateurs = $stmt->fetchAll();

// Récupérer l'utilisateur sélectionné
$receiver_id = isset($_GET['user']) ? (int)$_GET['user'] : 0;
$receiver_name = '';
$receiver_role = '';

if ($receiver_id) {
    $stmt = $pdo->prepare("SELECT nom, role FROM utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$receiver_id]);
    $receiver = $stmt->fetch();
    if ($receiver) {
        $receiver_name = $receiver['nom'];
        $receiver_role = $receiver['role'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroPastoral - Chat Direct</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f3fdf2; }
        .chat-box {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
        }/* Styles pour l'image de profil et l'effet de survol */
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
        .message {
            padding: 10px 15px;
            margin: 8px 0;
            border-radius: 20px;
            max-width: 70%;
            animation: fadeIn 0.4s ease;
        }
        .mine { background-color: #a6e6a3; text-align: right; margin-left: auto; }
        .theirs { background-color: #e3f8e2; text-align: left; margin-right: auto; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .user-list { border-right: 1px solid #ccc; padding-right: 20px; }
    </style>
</head>
<body>

<!-- Barre de navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">AgroPastoral</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="chat.php">Chat Online</a></li>
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
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="vendeur_dashboard.php">Retour</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Section Chat -->
<div class="container chat-box d-flex">
    <div class="col-3 user-list">
        <h5>Utilisateurs</h5>
        <ul class="list-group">
            <?php foreach ($utilisateurs as $u): ?>
                <a href="chat.php?user=<?= $u['id_utilisateur'] ?>" class="list-group-item <?= ($receiver_id == $u['id_utilisateur']) ? 'active' : '' ?>">
                    <?= getRoleIcon($u['role']) ?> <?= htmlspecialchars($u['nom']) ?>
                    <span class="badge <?= getRoleBadgeClass($u['role']) ?> ms-2">
                        <?= htmlspecialchars($u['role']) ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="col-9">
        <?php if ($abonnement_en_attente): ?>
            <div class="alert alert-warning text-center">
                <h4>⏳ Abonnement en attente !</h4>
                <p>Votre abonnement a été payé mais il n'a pas encore été activé par l'administrateur. Vous ne pouvez pas utiliser le chat pour le moment.</p>
            </div>
        <?php elseif ($receiver_id): ?>
            <h5 class="text-success">
                Discussion avec 
                <strong><?= getRoleIcon($receiver_role) ?> <?= htmlspecialchars($receiver_name) ?></strong>
                <span class="badge <?= getRoleBadgeClass($receiver_role) ?> ms-2">
                    <?= htmlspecialchars($receiver_role) ?>
                </span>
            </h5>
            <div id="chat-box" style="height: 300px; overflow-y: auto; margin-bottom: 15px;"></div>
            <div class="input-group">
                <input type="text" id="message-input" class="form-control" placeholder="Votre message...">
                <button class="btn btn-success" onclick="sendMessage()">Envoyer</button>
            </div>
        <?php else: ?>
            <div class="text-center text-muted mt-5">
                <h5>Aucun utilisateur sélectionné pour discuter.</h5>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pied de page -->
<footer class="text-center mt-5">
    <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>

<!-- Scripts -->
<script>
const currentUser = <?= $current_user ?>;
const receiverId = <?= $receiver_id ?>;

function sendMessage() {
    const input = document.getElementById("message-input");
    const message = input.value.trim();
    if (message === '') return;

    fetch('send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `receiver_id=${receiverId}&message=${encodeURIComponent(message)}`
    }).then(() => {
        input.value = '';
        loadMessages();
    });
}

function loadMessages() {
    fetch('recup_messages.php?receiver_id=' + receiverId)
        .then(res => res.json())
        .then(data => {
            const box = document.getElementById("chat-box");
            box.innerHTML = '';
            data.forEach(msg => {
                const div = document.createElement("div");
                div.className = "message " + (msg.sender_id == currentUser ? "mine" : "theirs");
                div.innerHTML = msg.message;
                box.appendChild(div);
            });
            box.scrollTop = box.scrollHeight;
        });
}

if (receiverId && !<?= $abonnement_en_attente ? 'true' : 'false' ?>) {
    loadMessages();
    setInterval(loadMessages, 3000);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
