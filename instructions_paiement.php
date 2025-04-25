<?php
session_start();

// Redirection si non connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexion.php");
    exit();
}

$id = $_SESSION['id_utilisateur'];

// Connexion DB
$pdo = new PDO("mysql:host=localhost;dbname=agropastoral;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fonction d'envoi de mail
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function envoyerMailAbonnement($nom, $email, $transaction, $montant) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'princeongala99@gmail.com';
        $mail->Password = 'zfjg gjmv saba lgbg';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('tonemail@gmail.com', 'Agropastoral');
        $mail->addAddress($email, $nom); // Utilisateur
        $mail->addAddress('princeongala99@gmail.com', 'Prince'); // Admin

        $mail->isHTML(false);
        $mail->Subject = 'Confirmation de votre abonnement';
        $mail->Body = "
Bonjour $nom,

Merci pour votre abonnement.

- Numéro de transaction : $transaction
- Montant : $montant FC

L'équipe Agropastoral vous remercie !
";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Erreur d'envoi de mail : " . $mail->ErrorInfo);
        return false;
    }
}

// Si l'utilisateur notifie son paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mise à jour dans la base
    $stmt = $pdo->prepare("UPDATE utilisateur SET paiement_effectue = 1 WHERE id_utilisateur = ?");
    $stmt->execute([$id]);

    // Récupérer les infos utilisateur
    $stmtInfos = $pdo->prepare("SELECT nom, email FROM utilisateur WHERE id_utilisateur = ?");
    $stmtInfos->execute([$id]);
    $user = $stmtInfos->fetch();

    // Appel de la fonction d'envoi de mail
    $nomComplet = $user['nom'];
    $email = $user['email'];
    $transaction = uniqid('TXN-'); // Génération d’un ID transaction simulé
    $montant = 2000;

    envoyerMailAbonnement($nomComplet, $email, $transaction, $montant);

    $message = "✅ Paiement notifié avec succès. Veuillez attendre la validation par l'admin.";
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Instructions de Paiement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">AgroPastoral</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="abonnement.php">Retour à l’abonnement</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h2 class="text-center mb-4">💳 Instructions de Paiement</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-success text-center"><?= $message ?></div>
    <?php endif; ?>

    <div class="card shadow p-4 mb-4">
        <h5>1️⃣ Étapes à suivre :</h5>
        <ul>
            <li>Envoyez <strong>2000 FCFA</strong> à l’un des numéros suivants :</li>
        </ul>
        <ul class="list-group mb-3">
            <li class="list-group-item">📱 Orange Money : <strong>77 123 4567</strong></li>
            <li class="list-group-item">📱 Wave : <strong>77 765 4321</strong></li>
            <li class="list-group-item">📱 Free Money : <strong>78 987 6543</strong></li>
        </ul>
        <p>Indiquez votre <strong>Nom</strong> ou <strong>ID Utilisateur</strong> dans le motif du paiement.</p>

        <hr>

        <h5>2️⃣ Une fois le paiement effectué :</h5>
        <form method="POST" class="text-center">
            <button type="submit" class="btn btn-primary">✅ J’ai payé - Notifier le paiement</button>
        </form>

        <div class="mt-3 text-muted text-center">
            <small>Un administrateur confirmera votre paiement sous peu.</small>
        </div>
    </div>
</div>

<footer class="text-center mt-5 mb-4">
    <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
