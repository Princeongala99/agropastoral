<?php
session_start();

// Connexion à la base de données
$host = 'localhost';
$dbname = 'agropastoral';
$user = 'root';
$pass = ''; // Mot de passe MySQL si nécessaire

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $username = trim($_POST['nom'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = "Veuillez remplir tous les champs.";
        header("Location: connexion_Admin.php");
        exit();
    }

    try {
        // Requête préparée pour récupérer l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE nom = :nom");
        $stmt->execute(['nom' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            // Vérification du mot de passe haché
            if (password_verify($password, $user['mot_de_passe_hash'])) {
                // Initialisation des variables de session
                $_SESSION['user_logged_in'] = true;
                $_SESSION['username'] = $user['nom'];
                $_SESSION['role'] = $user['role'];

                // Redirection selon le rôle
                if ($user['role'] === 'admin') {
                    header("Location: Admin.php");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Accès réservé aux administrateurs.";
                    header("Location: connexion_Admin.php");
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "Mot de passe incorrect.";
                header("Location: connexion_Admin.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Nom d'utilisateur introuvable.";
            header("Location: connexion_Admin.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erreur lors de la connexion : " . $e->getMessage();
        header("Location: connexion_Admin.php");
        exit();
    }
} else {
    // Redirection si la méthode n'est pas POST
    header("Location: connexion_Admin.php");
    exit();
}
?>