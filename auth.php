
<?php
session_start();

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}
