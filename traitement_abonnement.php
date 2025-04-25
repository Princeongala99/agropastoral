<?php
require_once 'mailer.php'; // ton script PHPMailer

// Vérifie que le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Vérifie que les champs sont remplis
    if (isset($_POST['nom'], $_POST['transaction'], $_POST['montant'])) {
        $nom = trim($_POST['nom']);
        $transaction = trim($_POST['transaction']);
        $montant = trim($_POST['montant']);

        // Récupérer l'email de l'utilisateur depuis la base de données
        $conn = new mysqli("localhost", "root", "", "agropastoral");

        if ($conn->connect_error) {
            die("Erreur de connexion : " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT email FROM utilisateur WHERE nom = ?");
        $stmt->bind_param("s", $nom);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();
        $conn->close();

        // Appel de la fonction d'envoi de mail
        if ($email && envoyerMailAbonnement($nom, $email, $transaction, $montant)) {
            echo "Mail envoyé avec succès !";
        } else {
            echo "Échec de l'envoi du mail.";
        }
    } else {
        echo "Tous les champs doivent être remplis.";
    }
} else {
    echo "Accès non autorisé.";
}