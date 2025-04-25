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

    // Requête d'insertion
    $sql = "INSERT INTO utilisateur (nom, email, mot_de_passe_hash, telephone, adresse, role)
            VALUES (:nom, :email, :motdepasse, :telephone, :adresse, :role)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':motdepasse', $motdepasse);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':role', $role);

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
