<?php
session_start();

$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "agropastoral";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['nom'], $_POST['prenom'], $_POST['password']) &&
        !empty(trim($_POST['nom'])) &&
        !empty(trim($_POST['prenom'])) &&
        !empty(trim($_POST['password'])))
    {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $password = trim($_POST['password']);
        
        if (strlen($password) < 4) {
            $_SESSION['error_message'] = "Le mot de passe doit contenir au moins 4 caractères.";
            header("Location: inscription.php");
            exit;
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($hashed_password === false) {
            error_log("Password hashing failed for user: " . $nom);
            $_SESSION['error_message'] = "Une erreur technique est survenue lors de la création du compte.";
            header("Location: inscription.php");
            exit;
        }

        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        // Check connection
        if ($conn->connect_error) {
            // Log the detailed error for the admin/developer
            error_log("Database Connection Error: " . $conn->connect_error);
            $_SESSION['error_message'] = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
            header("Location: inscription.php");
            exit;
        }

        $conn->set_charset("utf8mb4");

        $sql = "INSERT INTO utilisateurs (nom, prenom, mot_de_passe) VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare statement failed: " . $conn->error);
            $_SESSION['error_message'] = "Une erreur technique est survenue (prepare).";
            header("Location: inscription.php");
            $conn->close();
            exit;
        }

        $stmt->bind_param("sss", $nom, $prenom, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header("Location: connexion.php");

        } else {
            if ($conn->errno == 1062) {
                 $_SESSION['error_message'] = "Ce nom d'utilisateur ou une combinaison similaire existe déjà.";
            } else {
                 error_log("Execute statement failed: " . $stmt->error);
                 $_SESSION['error_message'] = "Une erreur est survenue lors de l'inscription: " . $stmt->error; // Consider showing a less detailed error to the user in production
            }
            header("Location: inscription.php");
        }

        $stmt->close();
        $conn->close();
        exit;

    } else {
        $_SESSION['error_message'] = "Veuillez remplir tous les champs obligatoires.";
        header("Location: inscription.php");
        exit;
    }

} else {
    header("Location: inscription.php");
    exit;
}
?>