<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit;
}

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

$message = "";

// Ajouter un utilisateur
if (isset($_POST['add_user'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $role = $_POST['role'];

    // Vérifie si l'email existe déjà
    $check = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetchColumn() > 0) {
        $message = "<div class='alert alert-danger'>Cet email est déjà utilisé.</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, email, role) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $email, $role]);
        header("Location: gestion_utilisateurs.php");
        exit;
    }
}

// Modifier un utilisateur
if (isset($_POST['update_user'])) {
    $id_utilisateur = $_POST['id_utilisateur'];
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $role = $_POST['role'];

    // Vérifie si l'email existe chez un autre utilisateur
    $check = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ? AND id_utilisateur != ?");
    $check->execute([$email, $id_utilisateur]);
    if ($check->fetchColumn() > 0) {
        $message = "<div class='alert alert-danger'>Cet email est déjà utilisé par un autre utilisateur.</div>";
    } else {
        $stmt = $pdo->prepare("UPDATE utilisateur SET nom = ?, email = ?, role = ? WHERE id_utilisateur = ?");
        $stmt->execute([$nom, $email, $role, $id_utilisateur]);
        header("Location: gestion_utilisateurs.php");
        exit;
    }
}

// Supprimer un utilisateur
if (isset($_GET['delete_user'])) {
    $id_utilisateur = $_GET['delete_user'];
    $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);
    header("Location: gestion_utilisateurs.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id_utilisateur, nom, email, role FROM utilisateur");
$stmt->execute();
$utilisateurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs - AgroPastoral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f3fdf2; }
        .user-card { background-color: #fff; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">AgroPastoral - Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Gestion Utilisateurs</a></li>
                <li class="nav-item"><a class="nav-link" href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2>Gestion des Utilisateurs</h2>

    <?= $message ?>

    <?php if (isset($_GET['edit_user'])): 
        $id_edit = $_GET['edit_user'];
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id_edit]);
        $edit_user = $stmt->fetch();
        if ($edit_user): ?>
        <div class="user-card">
            <h4>Modifier l'utilisateur</h4>
            <form method="POST">
                <input type="hidden" name="id_utilisateur" value="<?= $edit_user['id_utilisateur'] ?>">
                <div class="mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($edit_user['nom']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($edit_user['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rôle</label>
                    <select class="form-control" name="role" required>
                        <option value="admin" <?= $edit_user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="vendeur" <?= $edit_user['role'] == 'vendeur' ? 'selected' : '' ?>>Vendeur</option>
                        <option value="acheteur" <?= $edit_user['role'] == 'acheteur' ? 'selected' : '' ?>>Acheteur</option>
                    </select>
                </div>
                <button type="submit" name="update_user" class="btn btn-warning">Mettre à jour</button>
                <a href="gestion_utilisateurs.php" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    <?php endif; endif; ?>

    <div class="user-card">
        <h4>Ajouter un utilisateur</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Rôle</label>
                <select class="form-control" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="vendeur">Vendeur</option>
                    <option value="acheteur">Acheteur</option>
                </select>
            </div>
            <button type="submit" name="add_user" class="btn btn-success">Ajouter</button>
        </form>
    </div>

    <div class="user-card">
        <h4>Liste des Utilisateurs</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $u): ?>
                    <tr>
                        <td><?= $u['id_utilisateur'] ?></td>
                        <td><?= htmlspecialchars($u['nom']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td>
                            <a href="gestion_utilisateurs.php?edit_user=<?= $u['id_utilisateur'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="gestion_utilisateurs.php?delete_user=<?= $u['id_utilisateur'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<footer class="text-center mt-5">
    <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
