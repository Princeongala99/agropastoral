<?php
session_start();

// Vérification de la session utilisateur
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'agropastoral';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les données du formulaire
$nom = htmlspecialchars($_POST['nom']);
$email = htmlspecialchars($_POST['email']);
$telephone = htmlspecialchars($_POST['telephone']);
$adresse = htmlspecialchars($_POST['adresse']);
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Préparation de la requête de mise à jour
$sql = "UPDATE utilisateur SET nom = :nom, email = :email, telephone = :telephone, adresse = :adresse";

// Ajouter le mot de passe si l'utilisateur a choisi de le modifier
if (!empty($password)) {
    $password = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe
    $sql .= ", mot_de_passe = :mot_de_passe";
}

$sql .= " WHERE id_utilisateur = :id";

// Exécution de la requête
try {
    $stmt = $pdo->prepare($sql);
    
    // Lier les paramètres
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':adresse', $adresse);
    
    // Lier le mot de passe uniquement si nécessaire
    if (!empty($password)) {
        $stmt->bindParam(':mot_de_passe', $password);
    }
    
    $stmt->bindParam(':id', $_SESSION['id_utilisateur']);
    
    // Exécuter la requête
    $stmt->execute();
    
    // Message de confirmation
    $_SESSION['message'] = "Votre profil a été mis à jour avec succès!";
    
    // Redirection vers le dashboard de l'acheteur
    header('Location: acheteur_dashboard.php');
    exit();
} catch (PDOException $e) {
    // Gestion des erreurs
    $_SESSION['error'] = "Erreur lors de la mise à jour du profil : " . $e->getMessage();
    header('Location: modifier_profil.php');
    exit();
}
?>
