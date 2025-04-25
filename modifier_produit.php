<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=agropastoral;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id_produit = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];

    $stmt = $pdo->prepare("UPDATE produits SET nom=?, description=?, prix=? WHERE id_produit=?");
    $stmt->execute([$nom, $description, $prix, $id]);

    header("Location: gestion_produits.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un produit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h3>Modifier le produit</h3>
    <form method="post">
        <div class="mb-3">
            <label>Nom du produit</label>
            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($produit['nom']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($produit['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Prix (€)</label>
            <input type="number" step="0.01" name="prix" class="form-control" value="<?= $produit['prix'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>
</body>
</html>
