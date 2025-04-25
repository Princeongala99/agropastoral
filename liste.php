<?php
// listeproduit.php

// Connexion à la base de données

session_start();

$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "agropastoral";

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
// Récupération des produits
$query = "SELECT p.*, u.nom AS vendeur, u.telephone, u.localisation 
          FROM produits p 
          JOIN utilisateur u ON p.id_produit = u.id_utilisateur";
$result = $conn->query($query);

// Récupération des données météo (exemple avec OpenWeatherMap)
$api_key_meteo = "ecc80bca3aebf440ace97825036fca27";
$localisation_par_defaut = 'Bunia'; // Peut être modifié selon la localisation de l'utilisateur
$url_meteo = "http://api.openweathermap.org/data/2.5/weather?q=$localisation_par_defaut&appid=$api_key_meteo&units=metric&lang=fr";
$data_meteo = json_decode(file_get_contents($url_meteo), true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroMarket - Liste des Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .weather-card {
            background: linear-gradient(135deg, #72b5f7 0%, #2678d8 100%);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between align-items-center py-3 mb-4 border-bottom">
            <h1 class="h4">AgroMarket - Produits Agro-pastoraux</h1>
            
            <!-- Météo -->
            <div class="weather-card">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="mb-0"><?php echo $data_meteo['name']; ?></h5>
                        <div class="d-flex align-items-center">
                            <img src="http://openweathermap.org/img/wn/<?php echo $data_meteo['weather'][0]['icon']; ?>@2x.png" 
                                 alt="<?php echo $data_meteo['weather'][0]['description']; ?>" width="60">
                            <div>
                                <span class="fs-4"><?php echo round($data_meteo['main']['temp']); ?>°C</span>
                                <p class="mb-0 small"><?php echo ucfirst($data_meteo['weather'][0]['description']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Bouton Ajouter Produit -->
        <div class="mb-4">
            <a href="ajouterproduit.php" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Ajouter un Produit
            </a>
        </div>

        <!-- Liste des Produits -->
        <div class="row">
            <?php while($produit = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card product-card">
                    <img src="<?php echo $produit['image'] ?: 'images/produit-default.jpg'; ?>" class="card-img-top" alt="<?php echo $produit['nom']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $produit['nom']; ?></h5>
                        <p class="card-text"><?php echo $produit['description']; ?></p>
                        <p class="text-success fw-bold"><?php echo number_format($produit['prix'], 0, ',', ' '); ?> FCFA</p>
                        <p class="text-muted small">Vendeur: <?php echo $produit['vendeur']; ?></p>
                        
                        <div class="d-flex justify-content-between">
                            <!-- Bouton Commander -->
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#commanderModal<?php echo $produit['id_produit']; ?>">
                                <i class="fas fa-shopping-cart"></i> Commander
                            </button>
                            
                            <!-- Bouton Localisation -->
                            <button class="btn btn-info btn-sm" onclick="afficherLocalisation('<?php echo $produit['localisation']; ?>')">
                                <i class="fas fa-map-marker-alt"></i> Localisation
                            </button>
                            
                            <!-- Bouton Contacter -->
                            <a href="tel:<?php echo $produit['telephone']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-phone"></i> Contacter
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal pour commander -->
            <div class="modal fade" id="commanderModal<?php echo $produit['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Commander <?php echo $produit['nom']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="commandeForm<?php echo $produit['id']; ?>">
                                <div class="mb-3">
                                    <label for="quantite" class="form-label">Quantité</label>
                                    <input type="number" class="form-control" id="quantite" min="1" value="1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="adresse" class="form-label">Adresse de livraison</label>
                                    <textarea class="form-control" id="adresse" rows="2" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes supplémentaires</label>
                                    <textarea class="form-control" id="notes" rows="2"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary" onclick="validerCommande(<?php echo $produit['id']; ?>)">Valider la commande</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Modal pour la localisation -->
    <div class="modal fade" id="localisationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Localisation du vendeur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="map" style="height: 400px; width: 100%;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour afficher la localisation
        function afficherLocalisation(localisation) {
            // Ici, vous intégrerez l'API de cartographie comme Google Maps ou Leaflet
            // Pour l'exemple, nous affichons simplement l'adresse
            alert("Localisation du vendeur: " + localisation + "\n\n(Intégrez ici une carte avec l'API de votre choix)");
            
            // Pour une vraie implémentation:
            // 1. Initialiser la carte dans le modal
            // 2. Géocoder l'adresse pour obtenir les coordonnées
            // 3. Afficher un marqueur sur la carte
            $('#localisationModal').modal('show');
        }

        // Fonction pour valider la commande
        function validerCommande(produitId) {
            const quantite = document.getElementById('quantite').value;
            const adresse = document.getElementById('adresse').value;
            const notes = document.getElementById('notes').value;
            
            // Ici, vous enverriez les données au serveur via AJAX
            alert(`Commande validée!\nProduit ID: ${produitId}\nQuantité: ${quantite}\nAdresse: ${adresse}`);
            
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('commanderModal' + produitId));
            modal.hide();
            
            // Réinitialiser le formulaire
            document.getElementById('commandeForm' + produitId).reset();
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>