<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'agropastoral';
$user = 'root';
$pass = ''; // adapte selon ton environnement

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des rôles distincts depuis la colonne "roless" de la table "utilisateur"
$stmt = $pdo->query("SELECT DISTINCT roless FROM utilisateur");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire d'inscription</title>
    <style>
        form {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background: #f2f2f2;
            border-radius: 10px;
        }
        input, select, button {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            font-weight: bold;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<form method="POST" action="traitement_inscription.php">
    <input type="text" name="nom" placeholder="Nom" required>
    <input type="text" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <input type="text" name="telephone" placeholder="Téléphone" required>
    <input type="text" name="adresse" placeholder="Adresse" required>

    <select name="roless" required>
        <option value="">-- Sélectionnez un rôle --</option>
        <?php foreach ($roles as $role): ?>
            <option value="<?= htmlspecialchars($role['roless']) ?>">
                <?= htmlspecialchars($role['roless']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">S'inscrire</button>
</form>

</body>
</html>
