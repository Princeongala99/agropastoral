<?php
session_start();

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_SESSION['id_utilisateur'];

// Connexion DB
$pdo = new PDO("mysql:host=localhost;dbname=agropastoral;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupération des données de l'utilisateur
$stmt = $pdo->prepare("SELECT paiement_effectue, abonnement_actif, date_expiration FROM utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

$paiement = $user['paiement_effectue'];
$abonnement = $user['abonnement_actif'];
$date_expiration = $user['date_expiration'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Abonnement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a class="nav-link" href="acheteur_dashboard.php">Tableau de bord</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h2 class="text-center mb-4">💼 Mon Abonnement</h2>
    
    <?php if (!$paiement): ?>
        <div class="alert alert-warning text-center">
            <h5>⏳ Paiement non encore reçu</h5>
            <p>Merci de procéder au paiement pour activer votre abonnement.</p>
            <!-- Lien vers instructions de paiement -->
            <a href="instructions_paiement.php" class="btn btn-primary">Voir instructions de paiement</a>
        </div>
    <?php elseif ($paiement && !$abonnement): ?>
        <div class="alert alert-info text-center">
            <h5>🕒 Paiement reçu</h5>
            <p>Votre paiement a bien été reçu. L’abonnement est en cours d’activation par un administrateur. Veuillez patienter.</p>
        </div>
    <?php elseif ($abonnement): ?>
        <div class="alert alert-success text-center">
            <h5>✅ Abonnement actif</h5>
            <p>Votre abonnement est actif jusqu'au <strong><?= date('d/m/Y', strtotime($date_expiration)) ?></strong>.</p>
            <p>Vous avez accès aux fonctionnalités vendeurs.</p>
        </div>
    <?php endif; ?>
</div>

<footer class="text-center mt-5 mb-4">
    <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
