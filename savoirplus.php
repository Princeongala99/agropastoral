<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>En savoir plus - AgroPastoral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4a8c3a;
            --secondary-color: #3a6b2d;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
        }

        .header-banner {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .header-banner h1 {
            font-size: 3rem;
            font-weight: bold;
            animation: fadeInDown 1s ease-out;
        }
        
.carousel-item img {
    height: 500px;
    object-fit: cover;
    transition: transform 0.6s ease;
}
.carousel-item img:hover {
    transform: scale(1.02);
}



        .info-section {
            padding: 60px 0;
        }

        .info-icon {
            font-size: 3rem;
            color: var(--primary-color);
        }

        .info-box {
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            transition: transform 0.3s ease;
        }

        .info-box:hover {
            transform: translateY(-10px);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        footer {
    background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
    color: black; /* Texte blanc pour un bon contraste */
    padding: 40px 0 20px;
    text-align: center;
}

footer p {
    font-size: 1rem; /* Ajuste la taille de la police si nécessaire */
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2); /* Ajoute un léger ombrage pour améliorer la lisibilité */
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
                <li class="nav-item"><a class="nav-link" href="produits.php">Nos produits</a></li>
                <li class="nav-item"><a class="nav-link" href="ajoutproduit.php">Ajouter un produit</a></li>
                <li class="nav-item"><a class="nav-link active" href="savoirplus.php">En savoir plus</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="header-banner">
    <h1>🌿 En savoir plus sur AgroPastoral</h1>
</div>

<section class="container info-section">
    <div class="row text-center g-4">
        <div class="col-md-4">
            <div class="info-box">
                <div class="info-icon">🌾</div>
                <h4 class="mt-3">Notre mission</h4>
                <p>Nous valorisons les produits agricoles et pastoraux en créant un lien direct entre producteurs et consommateurs.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="info-box">
                <div class="info-icon">🚜</div>
                <h4 class="mt-3">Nos activités</h4>
                <p>Production, transformation, distribution et promotion de produits naturels issus de l’agriculture locale.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="info-box">
                <div class="info-icon">🌍</div>
                <h4 class="mt-3">Engagement durable</h4>
                <p>Nous adoptons des pratiques écologiques pour protéger l’environnement et soutenir l’économie rurale.</p>
            </div>
        </div>
    </div>
</section>

<section class="container text-center mb-5">
    <h3 class="mb-4">💡 Pourquoi nous choisir ?</h3>
    <p class="lead">Des produits frais, un circuit court, un impact positif sur les communautés rurales et un avenir plus vert.</p>
</section>
<!-- Section FAQ -->
<section class="container my-5">
    <h3 class="text-center mb-4">❓ Questions fréquentes</h3>
    <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="faq1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                    Quels types de produits proposez-vous ?
                </button>
            </h2>
            <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Nous proposons des produits issus de l’agriculture et de l’élevage : fruits, légumes, céréales, produits laitiers, etc.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="faq2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                    Est-ce que vous livrez à domicile ?
                </button>
            </h2>
            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Oui, nous proposons un service de livraison locale selon votre position.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="faq3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                    Peut-on visiter vos fermes ?
                </button>
            </h2>
            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Bien sûr ! Contactez-nous pour planifier une visite pédagogique ou une expérience fermière.
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section Témoignages -->
<section class="container my-5">
    <h3 class="text-center mb-4">💬 Témoignages de nos clients</h3>
    <div id="carouselTestimonials" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner text-center">
            <div class="carousel-item active">
                <blockquote class="blockquote">
                    <p class="mb-4">"Des produits frais et savoureux livrés à domicile. Un vrai plaisir !"</p>
                    <footer class="blockquote-footer">Sophie B., cliente fidèle</footer>
                </blockquote>
            </div>
            <div class="carousel-item">
                <blockquote class="blockquote">
                    <p class="mb-4">"Un engagement écologique réel. J’adore leur transparence et leur proximité."</p>
                    <footer class="blockquote-footer">Mamadou K., restaurateur</footer>
                </blockquote>
            </div>
            <div class="carousel-item">
                <blockquote class="blockquote">
                    <p class="mb-4">"Visiter la ferme avec mes enfants a été une superbe expérience éducative."</p>
                    <footer class="blockquote-footer">Nadia T., maman de 2 enfants</footer>
                </blockquote>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselTestimonials" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-success rounded-circle" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselTestimonials" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-success rounded-circle" aria-hidden="true"></span>
        </button>
    </div>
</section>

<footer>
    <div class="container">
        <p>&copy; <?php echo date("Y"); ?> AgroPastoral. Tous droits réservés.</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
