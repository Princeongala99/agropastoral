<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Acheteur</title>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <header>
        <h1>Bienvenue, Acheteur</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Tableau de bord</a></li>
                <li><a href="produits.php">Voir les produits</a></li>
                <li><a href="commandes.php">Mes commandes</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Produits disponibles</h2>
            <p>Consultez les produits disponibles et passez vos commandes.</p>
            <a href="produits.php" class="btn">Voir les produits</a>
        </section>

        <section>
            <h2>Historique des commandes</h2>
            <p>Consultez l'historique de vos commandes passées.</p>
            <a href="commandes.php" class="btn">Voir mes commandes</a>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Agropastoral. Tous droits réservés.</p>
    </footer>
</body>
</html>