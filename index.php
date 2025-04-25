<?php include 'auth.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroPastoral - Accueil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css"> 

    <style>
        .hero-section {
             background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            color: white;
            padding: 100px 20px;
        }
        .service-card {
            transition: transform 0.3s ease-in-out;
        }
        .service-card:hover {
            transform: scale(1.05);
        }
        .welcome {
            font-size: 1.2rem;
            color: #fff;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">AgroPastoral</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="chat.php"><i class="fas fa-comments"></i> Chat Online</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Section d'accueil -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4">Bienvenue sur AgroPastoral</h1>
        <p class="lead">Une plateforme moderne dédiée à l'agriculture et l'élevage durable.</p>
        <a href="produits.php" class="btn btn-light mt-3"><i class="fas fa-seedling"></i> Découvrir nos produits</a>
    </div>
</section>

<!-- Présentation des fonctionnalités -->
<section class="container py-5">
    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="card service-card shadow rounded-4 p-4">
                <i class="fas fa-tractor fa-3x text-success mb-3"></i>
                <h5>Produits Agricoles</h5>
                <p>Explorez notre catalogue de produits cultivés localement avec soin et durabilité.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card service-card shadow rounded-4 p-4">
                <i class="fas fa-cow fa-3x text-success mb-3"></i>
                <h5>Élevage</h5>
                <p>Suivez nos activités d'élevage et découvrez nos offres de bétail et produits laitiers.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card service-card shadow rounded-4 p-4">
                <i class="fas fa-comments fa-3x text-success mb-3"></i>
                <h5>Communication</h5>
                <p>Discutez en direct avec notre équipe ou entre membres de la communauté AgroPastoral.</p>
            </div>
        </div>
    </div>
</section>

<footer class="text-center mt-5 bg-light py-3">
  <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>