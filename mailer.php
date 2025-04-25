<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function envoyerMailAbonnement($nom, $email, $transaction, $montant) {
    $mail = new PHPMailer(true);

    try {
        // Paramètres SMTP Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'princeongala99@gmail.com'; // ⚠️ Ton adresse Gmail
        $mail->Password = 'ztfq emwr dhob xjrk';   // ⚠️ Ton mot de passe d'application
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Expéditeur
        $mail->setFrom('tonemail@gmail.com', 'Agropastoral');

        // Destinataires
        $mail->addAddress($email, $nom); // L'utilisateur
        $mail->addAddress('princeongala99@gmail.com', 'Admin');

        // Contenu
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