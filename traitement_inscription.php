<?php
// Connexion à la base de données
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

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupérer les valeurs du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $motdepasse = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telephone = htmlspecialchars($_POST['telephone']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $role = htmlspecialchars($_POST['role']);

    // Gestion de la photo
    $photo = NULL;  // Photo par défaut (si aucune photo téléchargée)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";  // Dossier où les photos seront stockées
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérification du type de fichier image
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo = $target_file;  // Enregistrer le chemin de la photo dans la base
            } else {
                die("Erreur lors de l'upload de la photo.");
            }
        } else {
            die("Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.");
        }
    }

    if ($_POST['password'] !== $_POST['confirmation_password']) {
        // Afficher un message d'erreur ou rediriger l'utilisateur
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }
    

    // Requête d'insertion
    $sql = "INSERT INTO utilisateur (nom, email, mot_de_passe_hash, telephone, adresse, role, photo)
            VALUES (:nom, :email, :motdepasse, :telephone, :adresse, :role, :photo)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':motdepasse', $motdepasse);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':photo', $photo);

    // Exécuter la requête
    if ($stmt->execute()) {
        // Rediriger après une inscription réussie
        header('Location: connexion.php');
        exit;
    } else {
        // Si l'insertion échoue
        die("Erreur lors de l'inscription.");
    }
} else {
    // Accès non autorisé
    die("Accès non autorisé.");
}
?>
