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

    <style>
        .required::after {
            content: ' *';
            color: red;
        }
        .form-message {
            font-size: 0.9em;
            color: gray;
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
                    <a class="nav-link" href="Accueil.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="connexion.php">Connexion</a>
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
        <h2 class="form-title">Créer un compte</h2> 
        <form method="POST" action="traitement_inscription.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nom" class="form-label required">Nom</label>
                <input type="text" name="nom" id="nom" class="form-control" placeholder="Nom" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label required">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
            </div>
            
            <div class="mb-3">
                <label for="telephone" class="form-label required">Téléphone</label>
                <input type="text" name="telephone" id="telephone" class="form-control" placeholder="Téléphone" required>
            </div>
            <div class="mb-3">
                <label for="adresse" class="form-label required">Adresse</label>
                <input type="text" name="adresse" id="adresse" class="form-control" placeholder="Adresse" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label required">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe" required>
            </div>
            <div class="mb-3">
                <label for="confirmation_password" class="form-label required">Confirmer le mot de passe</label>
                <input type="password" name="confirmation_password" id="confirmation_password" class="form-control" placeholder="Confirmer le mot de passe" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label required">Rôle</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="acheteur" selected>Acheteur</option>
                    <option value="vendeur">Vendeur</option>
                </select>
            </div>
            <div class="photo-upload mb-3">
                <label for="photo">Photo (facultatif)</label>
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-success">S'inscrire</button>
        </form>
        <div class="form-message mt-3">
            <p><span class="text-danger">*</span> Les champs marqués d'un astérisque sont obligatoires.</p>
        </div>
        <div class="link">
            <p>Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
        </div>
    </div>
</section>

<footer class="text-center mt-5">
  <p>&copy; 2025 AgroPastoral. Tous droits réservés.</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function triggerFileInput() {
        document.getElementById('photoInput').click();
    }

    function previewPhoto() {
        const file = document.getElementById('photoInput').files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const photoPreview = document.getElementById('photoPreview');
            const defaultIcon = document.getElementById('defaultIcon');

            photoPreview.src = e.target.result;
            photoPreview.style.display = 'block';
            defaultIcon.style.display = 'none';
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>

</body>
</html>
