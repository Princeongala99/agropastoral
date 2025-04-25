<?php
session_start();

$host = 'localhost';
$dbname = 'agropastoral';
$username = 'root';
$password = ''; // à adapter selon ton cas

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $passwordInput = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE nom = ?");
    $stmt->execute([$nom]);
    $user = $stmt->fetch();

    if ($user && password_verify($passwordInput, $user['mot_de_passe_hash'])) {
        // Authentification réussie
        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['role'];

        // Redirection selon le rôle
        switch ($user['role']) {
            case 'admin':
                header('Location: admin_dashboard.php');
                break;
            case 'vendeur':
                header('Location: vendeur_dashboard.php');
                break;
            case 'acheteur':
                header('Location: acheteur_dashboard.php');
                break;
            default:
                echo "<p>Rôle non reconnu.</p>";
        }
        exit();
    } else {
        echo "<p>Nom ou mot de passe incorrect.</p>";
    }
}
?>
