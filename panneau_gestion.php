<?php
session_start();

// Vérifie que l'utilisateur est un admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Connexion DB
$pdo = new PDO("mysql:host=localhost;dbname=agropastoral;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Action sur abonnement ou paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_utilisateur'];
    $action = $_POST['action'];

    if ($action === 'valider_paiement') {
        $stmt = $pdo->prepare("UPDATE utilisateur SET paiement_effectue = 1 WHERE id_utilisateur = ?");
    } elseif ($action === 'activer') {
        $stmt = $pdo->prepare("UPDATE utilisateur SET abonnement_actif = 1, date_expiration = DATE_ADD(NOW(), INTERVAL 30 DAY), role = 'vendeur' WHERE id_utilisateur = ?");
    } elseif ($action === 'desactiver') {
        $stmt = $pdo->prepare("UPDATE utilisateur SET abonnement_actif = 0, date_expiration = NULL WHERE id_utilisateur = ?");
    }

    if (isset($stmt)) {
        $stmt->execute([$id]);
    }
}

// Liste des utilisateurs avec info de paiement
$users = $pdo->query("SELECT id_utilisateur, nom, email, role, abonnement_actif, date_expiration, paiement_effectue FROM utilisateur")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Abonnements</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css"> 
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
                    <a class="nav-link" href="admin_dashboard.php">Retour</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="panneau_gestion.php">Gestion Abonnement</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section text-center">
    <div class="container py-4">
        <h2 class="mb-4">📋 Gestion des abonnements utilisateurs</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-success text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Paiement reçu</th>
                        <th>Abonné ?</th>
                        <th>Expire le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['id_utilisateur'] ?></td>
                            <td><?= htmlspecialchars($u['nom']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['role']) ?></td>
                            <td class="text-center">
                                <?= $u['paiement_effectue'] ? '💰 Oui' : '⛔ Non' ?>
                                <?php if (!$u['paiement_effectue']): ?>
                                    <form method="POST" class="d-inline mt-1">
                                        <input type="hidden" name="id_utilisateur" value="<?= $u['id_utilisateur'] ?>">
                                        <button name="action" value="valider_paiement" class="btn btn-sm btn-warning mt-1">Valider paiement</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?= $u['abonnement_actif'] ? '✅ Oui' : '❌ Non' ?>
                            </td>
                            <td class="text-center">
                                <?= $u['date_expiration'] ?? '---' ?>
                            </td>
                            <td class="text-center">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_utilisateur" value="<?= $u['id_utilisateur'] ?>">
                                    <?php if ($u['paiement_effectue']): ?>
                                        <button name="action" value="activer" class="btn btn-sm btn-success">Activer</button>
                                    <?php endif; ?>
                                    <button name="action" value="desactiver" class="btn btn-sm btn-danger">Désactiver</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<footer class="text-center mt-5 mb-4">
    <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
