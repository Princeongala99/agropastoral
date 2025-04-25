<<<<<<< HEAD
=======
<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un vendeur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'vendeur') {
    header("Location: connexion.php");
    exit();
}

// Traitement du formulaire d'abonnement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['souscrire'])) {
    // Ici vous devriez traiter le paiement (via Stripe, PayPal, etc.)
    // Pour cet exemple, nous marquons simplement que l'utilisateur a souscrit
    
    // Enregistrement en base de données
    $conn = new mysqli("localhost", "root", "", "agropast");
    if ($conn->connect_error) {
        die("Échec de connexion : " . $conn->connect_error);
    }
    
    $plan = $_POST['plan'];
    $date_debut = date('Y-m-d');
    $date_fin = date('Y-m-d', strtotime('+1 month')); // Exemple: abonnement d'1 mois
    
    $sql = "INSERT INTO abonnements (user_id, plan, date_debut, date_fin) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $_SESSION['user_id'], $plan, $date_debut, $date_fin);
    
    if ($stmt->execute()) {
        $_SESSION['abonnement_valide'] = true;
        header("Location: vendeur_dashboard.php");
        exit();
    } else {
        $error = "Erreur lors de l'enregistrement de l'abonnement. Veuillez réessayer.";
    }
    
    $stmt->close();
    $conn->close();
}
?>

>>>>>>> 45c14790dece1392286266e3a86a468be38f801b
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>AgroPastoral - Accueil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css"> 

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
                    <a class="nav-link" href="Accueil.php">Accueil</a>
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

<section class="hero-section text-center">
    <div class="form-container">
        <h2 class="form-title">Abonnement</h2> 
        <form method="POST" action="traitement_abonnement.php">
            <input type="text" name="nom" placeholder="Votre nom" required>
            <input type="text" name="transaction" placeholder="Numéro de transaction" required>
            <input type="number" name="montant" placeholder="Montant" required>
            <button type="submit">Confirmer</button>
        </form>
    </div>
</section>

<footer class="text-center mt-5">
  <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
=======
    <title>Abonnement Vendeur - AgroPastoral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #218838;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .pricing-card {
            border-radius: 10px;
            transition: all 0.3s;
            overflow: hidden;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .pricing-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px 0;
            text-align: center;
        }
        
        .pricing-title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .pricing-price {
            font-size: 3rem;
            font-weight: 700;
            margin: 20px 0;
        }
        
        .pricing-period {
            font-size: 1rem;
            opacity: 0.8;
        }
        
        .pricing-features {
            padding: 30px;
            background: white;
        }
        
        .pricing-features ul {
            list-style: none;
            padding: 0;
        }
        
        .pricing-features li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .pricing-features li:last-child {
            border-bottom: none;
        }
        
        .pricing-footer {
            padding: 20px;
            background: #f8f9fa;
            text-align: center;
        }
        
        .btn-premium {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            border: none;
            font-weight: 600;
        }
        
        .btn-standard {
            background: linear-gradient(135deg, #17a2b8, #138496);
            border: none;
            font-weight: 600;
        }
        
        .btn-basic {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            border: none;
            font-weight: 600;
        }
        
        .feature-icon {
            color: var(--primary-color);
            margin-right: 10px;
        }
        
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">AgroPastoral</a>
            <div class="ml-auto">
                <span class="navbar-text text-white">
                    Connecté en tant que: <strong><?php echo htmlspecialchars($_SESSION['user_nom']); ?></strong> (Vendeur)
                </span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">Devenez un vendeur AgroPastoral</h1>
            <p class="lead">Choisissez l'abonnement qui correspond à vos besoins et commencez à vendre vos produits dès aujourd'hui</p>
        </div>
    </section>

    <!-- Abonnement Section -->
    <div class="container py-5">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="text-center mb-5">
            <h2>Nos Plans d'Abonnement</h2>
            <p class="lead">Tous nos plans incluent une période d'essai de 7 jours</p>
        </div>
        
        <form method="POST" action="abonnement.php">
            <div class="row">
                <!-- Plan Basique -->
                <div class="col-md-4">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h3 class="pricing-title">Basique</h3>
                            <div class="pricing-price">
                                <span class="currency">€</span>9.99
                                <span class="pricing-period">/mois</span>
                            </div>
                        </div>
                        <div class="pricing-features">
                            <ul>
                                <li><i class="fas fa-check feature-icon"></i> 10 produits maximum</li>
                                <li><i class="fas fa-check feature-icon"></i> Statistiques de base</li>
                                <li><i class="fas fa-check feature-icon"></i> Support par email</li>
                                <li><i class="fas fa-times text-muted"></i> Mises en avant</li>
                                <li><i class="fas fa-times text-muted"></i> Analytics avancés</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            <button type="submit" name="plan" value="basique" class="btn btn-basic text-white btn-lg w-100">
                                Choisir ce plan
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Plan Standard (Recommandé) -->
                <div class="col-md-4">
                    <div class="pricing-card">
                        <div class="pricing-header position-relative">
                            <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-warning text-dark">
                                Le plus populaire
                            </span>
                            <h3 class="pricing-title">Standard</h3>
                            <div class="pricing-price">
                                <span class="currency">€</span>19.99
                                <span class="pricing-period">/mois</span>
                            </div>
                        </div>
                        <div class="pricing-features">
                            <ul>
                                <li><i class="fas fa-check feature-icon"></i> 50 produits maximum</li>
                                <li><i class="fas fa-check feature-icon"></i> Statistiques complètes</li>
                                <li><i class="fas fa-check feature-icon"></i> Support prioritaire</li>
                                <li><i class="fas fa-check feature-icon"></i> Mises en avant occasionnelles</li>
                                <li><i class="fas fa-times text-muted"></i> Analytics avancés</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            <button type="submit" name="plan" value="standard" class="btn btn-standard text-white btn-lg w-100">
                                Choisir ce plan
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Plan Premium -->
                <div class="col-md-4">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h3 class="pricing-title">Premium</h3>
                            <div class="pricing-price">
                                <span class="currency">€</span>29.99
                                <span class="pricing-period">/mois</span>
                            </div>
                        </div>
                        <div class="pricing-features">
                            <ul>
                                <li><i class="fas fa-check feature-icon"></i> Produits illimités</li>
                                <li><i class="fas fa-check feature-icon"></i> Statistiques avancées</li>
                                <li><i class="fas fa-check feature-icon"></i> Support 24/7</li>
                                <li><i class="fas fa-check feature-icon"></i> Mises en avant régulières</li>
                                <li><i class="fas fa-check feature-icon"></i> Analytics avancés</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            <button type="submit" name="plan" value="premium" class="btn btn-premium text-white btn-lg w-100">
                                Choisir ce plan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="souscrire" value="1">
        </form>
        
        <!-- Informations supplémentaires -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4"><i class="fas fa-question-circle me-2"></i>Questions fréquentes</h4>
                        
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        Comment fonctionne la période d'essai ?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Vous bénéficiez de 7 jours gratuits pour tester toutes les fonctionnalités de notre plateforme. Aucun paiement ne sera demandé pendant cette période.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                        Puis-je changer de plan plus tard ?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Oui, vous pouvez changer de plan à tout moment. La différence de prix sera calculée au prorata.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                        Quels moyens de paiement acceptez-vous ?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Nous acceptons les cartes de crédit (Visa, MasterCard), PayPal et les virements bancaires.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2023 AgroPastoral. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
>>>>>>> 45c14790dece1392286266e3a86a468be38f801b
