<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
            <div class="container">
                <a class="navbar-brand" href="#">AgroPastoral</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="produits.php">Produits</a></li>
                        <li class="nav-item"><a class="nav-link active" href="commandes.php">Mes Commandes</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container my-5">
        <h1 class="text-center mb-4">Historique de Mes Commandes</h1>
        <p class="text-center">Consultez les détails de vos commandes passées.</p>

        <!-- Table des commandes -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Prix Total</th>
                        <th>Date</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Exemple de commande -->
                    <tr>
                        <td>1</td>
                        <td>Produit A</td>
                        <td>2</td>
                        <td>40€</td>
                        <td>2025-04-20</td>
                        <td><span class="badge bg-success">Livré</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Produit B</td>
                        <td>1</td>
                        <td>20€</td>
                        <td>2025-04-18</td>
                        <td><span class="badge bg-warning text-dark">En cours</span></td>
                    </tr>
                    <!-- Ajouter d'autres commandes ici -->
                </tbody>
            </table>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> AgroPastoral - Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>