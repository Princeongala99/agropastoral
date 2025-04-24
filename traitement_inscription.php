<?php
session_start();

$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "agropast";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['nom'], $_POST['email'], $_POST['password'], $_POST['role'], $_POST['tel'], $_POST['address']) &&
        !empty(trim($_POST['nom'])) &&
        !empty(trim($_POST['email'])) &&
        !empty(trim($_POST['password'])) &&
        !empty(trim($_POST['role'])) &&
        !empty(trim($_POST['tel'])) &&
        !empty(trim($_POST['address'])))
    {
        $nom = trim($_POST['nom']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $role = trim($_POST['role']);
        $tel = trim($_POST['tel']);
        $address = trim($_POST['address']);
        
        // Validation du mot de passe
        if (strlen($password) < 4) {
            $_SESSION['error_message'] = "Le mot de passe doit contenir au moins 4 caractères.";
            header("Location: inscription.php");
            exit;
        }
        
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "L'adresse email n'est pas valide.";
            header("Location: inscription.php");
            exit;
        }
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($hashed_password === false) {
            error_log("Password hashing failed for user: " . $email);
            $_SESSION['error_message'] = "Une erreur technique est survenue lors de la création du compte.";
            header("Location: inscription.php");
            exit;
        }

        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        // Check connection
        if ($conn->connect_error) {
            error_log("Database Connection Error: " . $conn->connect_error);
            $_SESSION['error_message'] = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
            header("Location: inscription.php");
            exit;
        }

        $conn->set_charset("utf8mb4");

        $sql = "INSERT INTO utilisateur (nom, email, mot_de_passe_hash, role, telephone, adresse) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare statement failed: " . $conn->error);
            $_SESSION['error_message'] = "Une erreur technique est survenue (prepare).";
            header("Location: inscription.php");
            $conn->close();
            exit;
        }

        $stmt->bind_param("ssssss", $nom, $email, $hashed_password, $role, $tel, $address);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header("Location: connexion.php");
        } else {
            if ($conn->errno == 1062) {
                $_SESSION['error_message'] = "Cette adresse email est déjà utilisée.";
            } else {
                error_log("Execute statement failed: " . $stmt->error);
                $_SESSION['error_message'] = "Une erreur est survenue lors de l'inscription.";
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