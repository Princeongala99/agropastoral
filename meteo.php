<?php
$apiKey = "ecc80bca3aebf440ace97825036fca27";

$weatherData = null;
$errorMsg = null;

if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=fr";
} else {
    $city = isset($_GET['ville']) ? $_GET['ville'] : 'Bunia';
    if (!empty($city)) {
        $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric&lang=fr";
    }
}

if (isset($url)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($data && $data['cod'] == 200) {
        $weatherData = $data;
    } elseif ($data && isset($data['message'])) {
        $errorMsg = $data['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Météo | AgroPastoral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   

   <style>
        :root {
            --primary-color: #4a8c3a;
            --secondary-color: #3a6b2d;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 0;
        }

        form {
            margin-top: 30px;
        }

        input[type="text"] {
            padding: 10px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
        }

        button {
            padding: 10px 20px;
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1em;
            cursor: pointer;
            margin-left: 10px;
        }

        button:hover {
            background-color: var(--secondary-color);
        }

        .weather-container {
            background-color: white;
            padding: 30px;
            margin-top: 30px;
            border-radius: 15px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            animation: fadeIn 1s ease-in-out;
        }

        .weather-icon {
            width: 100px;
        }

        .temp {
            font-size: 2.5em;
            font-weight: bold;
        }

        .desc {
            font-size: 1.2em;
            color: #555;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-top: 20px;
        }

        footer {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .navbar-nav .nav-link {
    position: relative;
    transition: color 0.3s;
}

.navbar-nav .nav-link::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -5px;
    width: 0;
    height: 2px;
    background-color: white;
    transition: width 0.3s;
}

.navbar-nav .nav-link:hover::after {
    width: 100%;
}

                      /* Fade in de tout le formulaire */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-container {
    animation: fadeIn 1s ease-in-out;
}

/* Slide-in effet pour chaque champ */
input, button {
    opacity: 0;
    transform: translateY(20px);
    animation: slideUp 0.6s forwards;
}

input:nth-of-type(1) { animation-delay: 0.2s; }
input:nth-of-type(2) { animation-delay: 0.4s; }
button { animation-delay: 0.6s; }

@keyframes slideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hover animation sur le bouton */
button:hover {
    transform: scale(1.05);
    transition: transform 0.3s ease-in-out;
}

/* Petit effet au survol des liens */
.link a:hover {
    text-decoration: underline;
    transition: color 0.3s ease-in-out;
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
                <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="meteo.php">Météo</a></li>
                <li class="nav-item"><a class="nav-link" href="nosproduits.php">Nos productions</a></li>
                <li class="nav-item"><a class="nav-link" href="connexion.php">Connexion</a></li>
                <li class="nav-item"><a class="nav-link" href="inscription.php">Inscription</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section">
    <div class="container">
        <h1>🌤️ Vérifie la météo locale</h1>
        <form method="GET">
            <input type="text" name="ville" placeholder="Entrez une ville..." value="<?php echo isset($city) ? htmlspecialchars($city) : ''; ?>">
            <button type="submit">Voir la météo</button>
        </form>
    </div>
</section>

<?php if ($weatherData): ?>
    <div class="weather-container">
        <h2>Météo à <?php echo htmlspecialchars($weatherData['name']); ?></h2>
        <img class="weather-icon" src="https://openweathermap.org/img/wn/<?php echo $weatherData['weather'][0]['icon']; ?>@2x.png" alt="Icone météo">
        <div class="temp"><?php echo $weatherData['main']['temp']; ?>°C</div>
        <div class="desc"><?php echo ucfirst($weatherData['weather'][0]['description']); ?></div>
    </div>
<?php elseif ($errorMsg): ?>
    <div class="container text-center">
        <p class="error">Erreur : <?php echo htmlspecialchars($errorMsg); ?></p>
    </div>
<?php endif; ?>

<footer class="text-center">
    <div class="container">
        <p>&copy; <?php echo date("Y"); ?> AgroPastoral - Tous droits réservés.</p>
    </div>
</footer>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has('ville') && !urlParams.has('lat') && !urlParams.has('lon')) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    window.location.href = `?lat=${lat}&lon=${lon}`;
                },
                error => {
                    console.log("Géolocalisation refusée ou échouée.");
                }
            );
        }
    }
</script>
</body>
</html>
