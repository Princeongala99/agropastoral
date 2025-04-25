<?php
session_start();
if(!isset($_SESSION['id_utilisateur'])){
    header(header:'location:connexion.php');
    exit();
}