<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

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

// Récupérer les articles du panier de l'utilisateur
$stmt = $pdo->prepare("SELECT p.*, pr.nom, pr.prix, pr.quantite AS stock_disponible 
                       FROM panier p 
                       JOIN produits pr ON p.id_produit = pr.id_produit 
                       WHERE p.id_acheteur = ?");
$stmt->execute([$id_utilisateur]);
$panier_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($panier_items)) {
    $_SESSION['message'] = "Votre panier est vide.";
    header('Location: panier.php');
    exit();
}

// Parcourir les articles du panier
foreach ($panier_items as $item) {
    $id_produit = $item['id_produit'];
    $quantite = $item['quantite'];
    $prix_unitaire = $item['prix'];
    $total = $quantite * $prix_unitaire;
    $date_commande = date('Y-m-d H:i:s');

    // Vérifier que le stock est suffisant
    if ($item['stock_disponible'] < $quantite) {
        $_SESSION['message'] = "Stock insuffisant pour le produit : " . htmlspecialchars($item['nom_produit']);
        header('Location: panier.php');
        exit();
    }

    // Réduire le stock
    $update = $pdo->prepare("UPDATE produits SET quantite = quantite - :qte WHERE id_produit = :id");
    $update->execute([
        'qte' => $quantite,
        'id' => $id_produit
    ]);

    // Enregistrer la commande
    $insert = $pdo->prepare("INSERT INTO commandes (id_utilisateur, id_produit, quantite, date_commande, total) 
                             VALUES (:id_utilisateur, :id_produit, :quantite, :date_commande, :total)");
    $insert->execute([
        'id_utilisateur' => $id_utilisateur,
        'id_produit' => $id_produit,
        'quantite' => $quantite,
        'date_commande' => $date_commande,
        'total' => $total
    ]);
}

// Vider le panier
$delete = $pdo->prepare("DELETE FROM panier WHERE id_acheteur = ?");
$delete->execute([$id_utilisateur]);

$_SESSION['message'] = "Commande passée avec succès !";
header("Location: mes_commandes.php");
exit();
