<?php
include 'db.php';

$sql = "
    SELECT u.id, u.nom, p.latitude, p.longitude, pr.nom_produit
    FROM utilisateurs u
    JOIN positions p ON u.id = p.id_utilisateur
    LEFT JOIN produits pr ON u.id = pr.id_utilisateur
    WHERE u.role = 'vendeur'
";

$result = $conn->query($sql);

$vendeurs = [];

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    if (!isset($vendeurs[$id])) {
        $vendeurs[$id] = [
            'id' => $id,
            'nom' => $row['nom'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'produits' => []
        ];
    }
    if ($row['nom_produit']) {
        $vendeurs[$id]['produits'][] = $row['nom_produit'];
    }
}

echo json_encode(array_values($vendeurs));
?>
