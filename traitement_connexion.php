<?php
session_start();

// Configuration de la base de données
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "agropast";

// Vérification de la méthode de requête
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation des champs
    if (empty(trim($_POST['nom']))) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'title' => 'Champ requis',
            'message' => 'Le nom d\'utilisateur est obligatoire pour la connexion.'
        ];
        header("Location: connexion.php");
        exit;
    }

    if (empty(trim($_POST['password']))) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'title' => 'Champ requis',
            'message' => 'Veuillez saisir votre mot de passe pour continuer.'
        ];
        header("Location: connexion.php");
        exit;
    }

    // Nettoyage des données
    $nom = trim($_POST['nom']);
    $password = trim($_POST['password']);

    try {
        // Connexion à la base de données
        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        // Vérification de la connexion
        if ($conn->connect_error) {
            throw new Exception("Erreur de connexion à la base de données");
        }

        $conn->set_charset("utf8mb4");

        // Requête préparée pour la sécurité
        $sql = "SELECT id_utilisateur, nom, email, mot_de_passe_hash, role FROM utilisateur WHERE nom = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête");
        }

        $stmt->bind_param("s", $nom);
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérification de l'utilisateur
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Vérification du mot de passe
            if (password_verify($password, $user['mot_de_passe_hash'])) {
                // Connexion réussie - Initialisation de la session
                $_SESSION['user_id'] = $user['id_utilisateur'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                
                // Notification de succès
                $_SESSION['notification'] = [
                    'type' => 'success',
                    'title' => 'Connexion réussie',
                    'message' => 'Vous êtes maintenant connecté à votre compte.'
                ];
                
                header("Location: produits.php");
                exit;
            } else {
                $_SESSION['notification'] = [
                    'type' => 'error',
                    'title' => 'Échec de connexion',
                    'message' => 'La combinaison nom d\'utilisateur/mot de passe est incorrecte.'
                ];
            }
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'title' => 'Compte introuvable',
                'message' => 'Aucun compte ne correspond à ces identifiants.'
            ];
        }

        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        error_log($e->getMessage());
        $_SESSION['notification'] = [
            'type' => 'error',
            'title' => 'Erreur technique',
            'message' => 'Un problème est survenu lors de la connexion. Notre équipe a été notifiée.'
        ];
    }

    // Redirection en cas d'échec
    header("Location: connexion.php");
    exit;
} else {
    // Accès non autorisé
    $_SESSION['notification'] = [
        'type' => 'warning',
        'title' => 'Accès non autorisé',
        'message' => 'Vous devez utiliser le formulaire de connexion pour accéder à cette page.'
    ];
    header("Location: accueil.php");
    exit;
}
?>