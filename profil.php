<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
    </script>
</body>
</html>