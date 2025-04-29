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
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des infos de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT role, abonnement_actif, nom, photo FROM utilisateur WHERE id_utilisateur = :id");
$stmt->execute(['id' => $_SESSION['id_utilisateur']]);
$user = $stmt->fetch();

if ($user) {
    $_SESSION['role'] = $user['role'];
    $_SESSION['abonnement_actif'] = $user['abonnement_actif'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['photo'] = $user['photo'];
} else {
    die("Utilisateur introuvable.");
}


if (!isset($_SESSION['id_utilisateur'])) {
    die("Vous devez être connecté pour voir votre panier.");
}

$id_acheteur = $_SESSION['id_utilisateur'];

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

// Supprimer un produit du panier (via id_panier)
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id_panier = (int) $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM panier WHERE id_panier = ? AND id_acheteur = ?");
    $stmt->execute([$id_panier, $id_acheteur]);
    header('Location: panier.php');
    exit();
}

// Récupérer les produits du panier pour l'utilisateur connecté
$stmt = $pdo->prepare("SELECT p.*, pr.image, pr.prix FROM panier p JOIN produits pr ON p.id_produit = pr.id_produit WHERE p.id_acheteur = ?");
$stmt->execute([$id_acheteur]);
$panier_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcul du total
$total = 0;
foreach ($panier_items as $item) {
    $total += $item['prix'] * $item['quantite'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --agro-primary: #2E8B57;
            --agro-dark: #006400;
        }
        .btn-agro { background-color: var(--agro-primary); color: white; }
        .btn-agro:hover { background-color: var(--agro-dark); }
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
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="index.php">AgroPastoral</a>
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
    </div>
</nav>

<div class="container py-5">
    <h2>Mon Panier</h2>

    <?php if (empty($panier_items)): ?>
        <div class="alert alert-info">Votre panier est vide.</div>
        <a href="acheteur_dashboard.php" class="btn btn-agro">Retour à la boutique</a>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($panier_items as $item): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top" style="height: 200px; width:200px; align-self:center;">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($item['nom_produit']) ?></h5>
                            <p>Prix : <?= $item['prix'] ?> FC</p>
                            <p>Quantité : <?= $item['quantite'] ?></p>
                            <p>Total : <strong><?= $item['prix'] * $item['quantite'] ?> FC</strong></p>
                            <a href="panier.php?action=supprimer&id=<?= $item['id_panier'] ?>" class="btn btn-danger w-100" onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-5 text-end">
            <h4>Total général : <?= $total ?> FC</h4>
            <form action="gestion_commandes.php" method="post">
                <button type="submit" class="btn btn-agro btn-lg mt-2">Passer la commande</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
