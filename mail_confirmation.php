<?php
use PHPMailer\src\PHPMailer;
use PHPMailer\src\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Exemple : Gmail
    $mail->SMTPAuth   = true;
    $mail->Username   = 'princeongala99@gmail.com'; // Remplace par ton email
    $mail->Password   = '123456'; // Remplace par ton mot de passe ou un mot de passe d'application
    $mail->SMTPSecure = 'tls'; // ou 'ssl'
    $mail->Port       = 587; // ou 465 pour 'ssl'

    // Destinataires
    $mail->setFrom('princeongala99@gmail.com', 'AgroPastoral');
    $mail->addAddress('princeongala99@gmail.com', 'Administrateur'); // Mail de l'admin

    // Contenu
    $mail->isHTML(true);
    $mail->Subject = 'Confirmation de paiement';
    $mail->Body    = "<h3>Un paiement vient d'être effectué sur AgroPastoral</h3>
                      <p><strong>Nom du client :</strong> Jean Dupont</p>
                      <p><strong>Montant :</strong> 5.000 FCFA</p>
                      <p><strong>Date :</strong> " . date('d/m/Y H:i') . "</p>";

    $mail->send();
    echo 'Message envoyé à l’administrateur avec succès.';
} catch (Exception $e) {
    echo "Le message n’a pas pu être envoyé. Erreur : {$mail->ErrorInfo}";
}
