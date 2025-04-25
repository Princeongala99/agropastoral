<<<<<<< HEAD
=======
<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "agropast");
if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $description = $_POST['description'];
    $id = $_SESSION['user_id'];

    // Gestion de l'upload de la photo de profil
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $targetDir = "uploads/profils/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDir . $newFileName;

        // Vérifier le type de fichier
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                // Supprimer l'ancienne photo si elle existe
                if (!empty($_SESSION['user_photo']) && file_exists($_SESSION['user_photo'])) {
                    unlink($_SESSION['user_photo']);
                }
                
                // Mettre à jour la photo dans la session et la base de données
                $_SESSION['user_photo'] = $targetFile;
                $sql = "UPDATE utilisateur SET photo = ? WHERE id_utilisateur = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $targetFile, $id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Mettre à jour les autres informations
    $sql = "UPDATE utilisateur SET nom = ?, email = ?, telephone = ?, adresse = ?, description = ? WHERE id_utilisateur = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nom, $email, $telephone, $adresse, $description, $id);
    
    if ($stmt->execute()) {
        // Mettre à jour les informations dans la session
        $_SESSION['user_nom'] = $nom;
        $success = "Profil mis à jour avec succès!";
    } else {
        $error = "Erreur lors de la mise à jour du profil.";
    }
    $stmt->close();
}

// Récupérer les informations actuelles de l'utilisateur
$sql = "SELECT * FROM utilisateur WHERE id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

>>>>>>> 17612fcb89b0f88beb404ae003898b24eddd04fc
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Mon Profil</title>
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
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Tableau de bord</a></li>
                        <li class="nav-item"><a class="nav-link" href="produits.php">Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="commandes.php">Mes Commandes</a></li>
                        <li class="nav-item"><a class="nav-link active" href="profil.php">Mon Profil</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container my-5">
        <h1 class="text-center mb-4">Mon Profil</h1>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="update_profil.php" method="POST" enctype="multipart/form-data">
                            <!-- Photo de profil -->
                            <div class="text-center mb-4">
                                <img src="https://via.placeholder.com/150" alt="Photo de profil" class="rounded-circle img-thumbnail" id="profileImage">
                                <div class="mt-3">
                                    <label for="profilePhoto" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-upload"></i> Modifier la photo
                                    </label>
                                    <input type="file" id="profilePhoto" name="profilePhoto" class="d-none" onchange="previewImage(event)">
                                </div>
                            </div>

                            <!-- Nom -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom complet</label>
                                <input type="text" class="form-control" id="name" name="name" value="Mohamed Ali" required>
                            </div>

                            <!-- Contact -->
                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact</label>
                                <input type="text" class="form-control" id="contact" name="contact" value="mohamed.ali.ben@example.com" required>
                            </div>

                            <!-- Bouton de soumission -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
=======
    <title>Mon Profil - AgroPastoral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, #28a745, #218838);
            height: 150px;
            position: relative;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            position: absolute;
            bottom: -75px;
            left: 50%;
            transform: translateX(-50%);
            object-fit: cover;
        }
        .profile-body {
            padding-top: 90px;
            padding-bottom: 30px;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
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
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="vendeur_dashboard.php">Tableau de bord</a></li>
                <li class="nav-item"><a class="nav-link" href="produits.php">Mes Produits</a></li>
                <li class="nav-item"><a class="nav-link" href="ajoutproduit.php">Ajouter Produit</a></li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <?php if (!empty($_SESSION['user_photo'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_photo']); ?>" alt="Photo de profil" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profil.php"><i class="fas fa-user me-2"></i>Mon Profil</a></li>
                        <li><a class="dropdown-item" href="parametres.php"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="accueil.php"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if(isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="card profile-card mb-5">
                    <div class="profile-header"></div>
                    <div class="text-center">
                        <img src="<?php echo !empty($user['photo']) ? htmlspecialchars($user['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($user['nom']).'&size=150&background=random'; ?>" 
                             alt="Photo de profil" 
                             class="profile-img">
                    </div>
                    <div class="card-body profile-body text-center">
                        <h3 class="mb-3"><?php echo htmlspecialchars($user['nom']); ?></h3>
                        <p class="text-muted mb-4"><?php echo htmlspecialchars($user['description'] ?? 'Vendeur AgroPastoral'); ?></p>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Modifier mon profil</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom complet</label>
                                    <input type="text" class="form-control" id="nom" name="nom" 
                                           value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone" 
                                           value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="adresse" class="form-label">Adresse</label>
                                    <input type="text" class="form-control" id="adresse" name="adresse" 
                                           value="<?php echo htmlspecialchars($user['adresse'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($user['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="photo" class="form-label">Photo de profil</label>
                                <input class="form-control" type="file" id="photo" name="photo" accept="image/*">
                                <small class="text-muted">Formats acceptés: JPG, PNG, GIF (max 2MB)</small>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-success px-4 py-2">
                                    <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                </button>
>>>>>>> 17612fcb89b0f88beb404ae003898b24eddd04fc
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<<<<<<< HEAD
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> AgroPastoral - Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Prévisualisation de l'image
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('profileImage');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
=======
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Aperçu de l'image avant upload
        document.getElementById('photo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-img').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
>>>>>>> 17612fcb89b0f88beb404ae003898b24eddd04fc
    </script>
</body>
</html>