<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Acheteur</title>
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
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Tableau de bord</a></li>
                        <li class="nav-item"><a class="nav-link" href="produits.php">Voir les produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="commandes.php">Mes commandes</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
                        <!-- Icône de profil -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle fa-lg"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="profil.php">Mon Profil</a></li>
                                <li><a class="dropdown-item" href="settings.php">Paramètres</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container my-5">
        <!-- Produits disponibles -->
        <section class="mb-5">
            <h2 class="text-center mb-4">Produits disponibles</h2>
            <p class="text-center">Explorez notre catalogue de produits et passez vos commandes en toute simplicité.</p>
            <div class="text-center">
                <a href="produits.php" class="btn btn-primary">Voir les produits</a>
            </div>
        </section>

        <!-- Historique des commandes -->
        <section>
            <h2 class="text-center mb-4">Historique des commandes</h2>
            <p class="text-center">Consultez l'historique de vos commandes passées.</p>
            <div class="text-center">
                <a href="commandes.php" class="btn btn-secondary">Voir mes commandes</a>
            </div>
        </section>
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