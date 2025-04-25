<?php
session_start();

// Vérification de l'utilisateur connecté
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'vendeur') {
    $_SESSION['redirect_after_login'] = 'abonnement.php';
    header("Location: connexion.php");
    exit();
}

// Connexion à la base
$conn = new mysqli("localhost", "root", "", "agropast");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['souscrire'])) {
    // Données du formulaire
    $plan = $_POST['plan'];
    $montant = [
        'basique' => 9.99,
        'standard' => 19.99,
        'premium' => 29.99
    ][$plan];
    
    $date_paiement = date('Y-m-d H:i:s');
    $numero_transaction = 'TRX-' . uniqid();
    $id_vendeur = $_SESSION['id_utilisateur'];

    // Requête adaptée à votre structure
    $sql = "INSERT INTO abonnement (id_vendeur, plan, montant, numero_transaction, date_paiement) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur préparation: " . $conn->error);
    }

    $stmt->bind_param("issss", $id_vendeur, $plan, $montant, $numero_transaction, $date_paiement);
    
    if ($stmt->execute()) {
        // Mettre à jour la session
        $_SESSION['abonnement_valide'] = true;
        $_SESSION['abonnement_plan'] = $plan;
        
        // Redirection
        header("Location: interfacevendeur.php?success=Abonnement activé");
        exit();
    } else {
        $error = "Erreur : " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- ... (votre entête existant) ... -->
    <style>
        .plan-card {
            transition: all 0.3s;
        }
        .plan-card:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .badge-popular {
            position: absolute;
            top: -10px;
            right: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <div class="text-center mb-5">
                    <h2>Choisissez votre formule d'abonnement</h2>
                    <p class="lead">Tous les abonnements incluent une période d'essai de 7 jours</p>
                </div>

                <form method="POST">
                    <div class="row">
                        <!-- Carte Basique -->
                        <div class="col-md-4 mb-4">
                            <div class="card plan-card h-100">
                                <div class="card-header bg-secondary text-white">
                                    <h4 class="text-center">Basique</h4>
                                </div>
                                <div class="card-body text-center">
                                    <h3 class="card-title">9,99€<small class="text-muted">/mois</small></h3>
                                    <ul class="list-unstyled mt-3 mb-4">
                                        <li>10 produits max</li>
                                        <li>Statistiques de base</li>
                                        <li>Support par email</li>
                                    </ul>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button type="submit" name="plan" value="basique" 
                                            class="btn btn-outline-secondary w-100">
                                        Choisir
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Carte Standard -->
                        <div class="col-md-4 mb-4">
                            <div class="card plan-card h-100 border-primary">
                                <div class="card-header bg-primary text-white position-relative">
                                    <span class="badge bg-warning text-dark badge-popular">Populaire</span>
                                    <h4 class="text-center">Standard</h4>
                                </div>
                                <div class="card-body text-center">
                                    <h3 class="card-title">19,99€<small class="text-muted">/mois</small></h3>
                                    <ul class="list-unstyled mt-3 mb-4">
                                        <li>50 produits max</li>
                                        <li>Statistiques complètes</li>
                                        <li>Support prioritaire</li>
                                    </ul>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button type="submit" name="plan" value="standard" 
                                            class="btn btn-primary w-100">
                                        Choisir
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Carte Premium -->
                        <div class="col-md-4 mb-4">
                            <div class="card plan-card h-100 border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h4 class="text-center">Premium</h4>
                                </div>
                                <div class="card-body text-center">
                                    <h3 class="card-title">29,99€<small class="text-muted">/mois</small></h3>
                                    <ul class="list-unstyled mt-3 mb-4">
                                        <li>Produits illimités</li>
                                        <li>Statistiques avancées</li>
                                        <li>Support 24/7</li>
                                    </ul>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button type="submit" name="plan" value="premium" 
                                            class="btn btn-warning w-100">
                                        Choisir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="souscrire" value="1">
                </form>
                
                <div class="card mt-4">
                    <div class="card-body">
                        <h5><i class="fas fa-info-circle"></i> Informations importantes</h5>
                        <ul>
                            <li>Paiement sécurisé via notre plateforme</li>
                            <li>Facture disponible dans votre espace vendeur</li>
                            <li>Résiliation possible à tout moment</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>