<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroPastoral - Accueil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">AgroPastoral</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="meteo.php">Meteo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="produits.php">Nos productions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="connexion.php">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inscription.php">Inscription</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Bienvenue à la Plateforme AgroPastorale</h1>
            <p class="lead mb-4">Découvrez notre engagement pour une agriculture durable et respectueux de l'environnement.</p>
            <a href="produits.php" class="btn btn-agro btn-lg">Découvrir nos produits</a>
        </div>
    </section>

    <!-- À propos -->
    <section class="container mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Notre Philosophie</h2>
                <p class="lead">Depuis 2025, nous cultivons la terre avec passion et respect pour l'environnement.</p>
                <p>Notre plateforme agro-pastorale intègre des pratiques agricoles durables, créant ainsi un écosystème équilibré et productif. Nous croyons en une agriculture qui nourrit tout en préservant les ressources pour les générations futures.</p>
                <a href="savoirplus.php" class="btn btn-agro mt-3">En savoir plus</a>
            </div>
           
        </div>
    </section>

    <!-- Nos services -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Nos Domaines d'Expertise</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card service-card h-100">
                        <img src="https://images.unsplash.com/photo-1500595046743-cd271d694d30?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" class="card-img-top" alt="Élevage bovin">
                        <div class="card-body text-center">
                            <i class="fas fa-cow service-icon"></i>
                            <h3 class="card-title">Élevage Bovin</h3>
                            <p class="card-text">Notre cheptel de vaches laitières et à viande est élevé dans le respect des normes biologiques et du bien-être animal.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card h-100">
                        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" class="card-img-top" alt="Culture céréalière">
                        <div class="card-body text-center">
                            <i class="fas fa-wheat-awn service-icon"></i>
                            <h3 class="card-title">Culture Céréalière</h3>
                            <p class="card-text">Production de blé, orge et maïs selon des méthodes agricoles durables préservant les sols et la biodiversité.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card h-100">
                        <img src="https://images.unsplash.com/photo-1518977676601-b53f82aba655?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" class="card-img-top" alt="Produits fermiers">
                        <div class="card-body text-center">
                            <i class="fas fa-cheese service-icon"></i>
                            <h3 class="card-title">Produits Fermiers</h3>
                            <p class="card-text">Fromages, yaourts, viandes et autres produits transformés directement à la ferme avec nos matières premières.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h3 class="fw-bold mb-3">AgroPastoral</h3>
                    <p>L'excellence agricole depuis 2025.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h3 class="fw-bold mb-3">Contact</h3>
                    <p><i class="fas fa-map-marker-alt me-2"></i>UNIVERSITE SHALOM DE BUNIA</p>
                    <p><i class="fas fa-phone me-2"></i> +243 830 643 522</p>
                </div>
                <div class="col-md-4">
                    <h3 class="fw-bold mb-3">Réseaux sociaux</h3>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="https://www.facebook.com/unishabunia" class="text-white"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="https://www.youtube.com/@universiteshalomdebuniausb2150" class="text-white"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> AgroPastoral - Tous droits réservés</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>