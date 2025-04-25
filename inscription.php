<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroPastoral - Accueil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="text" name="telephone" placeholder="Téléphone" required>
            <input type="text" name="adresse" placeholder="Adresse" required>
            <select id="role" name="role" required>
               <option value="acheteur" selected>Acheteur</option>
               <option value="vendeur" selected>Vendeur</option>
            </select> 
            <div class="photo-upload">
                <label for="photo">Photo (facultatif)</label>
                <input type="file" name="photo" id="photo" accept="image/*">
            </div>

            <button type="submit">S'inscrire</button>
        </form>
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

</body>
</html>
