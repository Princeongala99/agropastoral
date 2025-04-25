
<?php
session_start();

// Paramètres de connexion à la bd
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'agropastoral';

// Vérifie si la requête est bien un POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $nom = trim($_POST['nom'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Vérifie que les champs ne sont pas vides
    if (empty($nom) || empty($password)) {
        $_SESSION['error'] = "Veuillez remplir tous les champs.";
        header('Location: connexion.php');
        exit();
    }

    // Connexion à la base de données
    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Erreur de connexion à la base de données : " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

    // Requête préparée sécurisée
    $stmt = $conn->prepare("SELECT id_utilisateur, nom, mot_de_passe_hash FROM utilisateur WHERE nom = ?");
    $stmt->bind_param("s", $nom);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si on trouve l'utilisateur
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Vérifie le mot de passe avec password_verify
        if (password_verify($password, $user['mot_de_passe_hash'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_id'] = $user['id_utilisateur'];

            header('Location: produits.php');
            exit();
        } else {
            $_SESSION['error'] = "Mot de passe incorrect.";
            header('Location: connexion.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Nom d'utilisateur introuvable.";
        header('Location: connexion.php');
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // Accès direct interdit
    header('Location: connexion.php');
    exit();
}

