<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5">
        <title>Kel Foncia RDC • Le logiciel du foncier professionnel</title>
        <!-- Google Fonts (police sobre et pro) -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <!-- Font Awesome 6 (gratuit) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <!-- Leaflet pour la carte interactive statique (OpenStreetMap) -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            /* ---------- VARIABLES & RESET ---------- */
            :root {
                --bleu-pro: #0a3143;
                --bleu-clair: #1e4b5e;
                --or: #c79a3e;
                --gris-fonce: #2d3e4f;
                --gris-moyen: #5a6b7a;
                --gris-clair: #f0f3f7;
                --blanc: #ffffff;
                --ombre: 0 15px 35px rgba(10, 49, 67, 0.08);
                --border-radius: 12px;
                --transition: all 0.25s ease;
            }
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: 'Inter', sans-serif;
                color: var(--gris-fonce);
                line-height: 1.5;
                background-color: var(--blanc);
                overflow-x: hidden;
            }
            a {
                text-decoration: none;
                color: inherit;
            }
            .container {
                width: 100%;
                max-width: 1280px;
                margin: 0 auto;
                padding: 0 32px;
            }
            /* ---------- TYPO & UTILS ---------- */
            h1, h2, h3, h4 {
                font-weight: 700;
                line-height: 1.2;
            }
            .section-title {
                font-size: 2.2rem;
                letter-spacing: -0.02em;
                margin-bottom: 1rem;
                color: var(--bleu-pro);
            }
            .section-sub {
                font-size: 1.2rem;
                font-weight: 400;
                color: var(--gris-moyen);
                max-width: 700px;
                margin-bottom: 3rem;
            }
            .btn {
                display: inline-block;
                padding: 14px 32px;
                border-radius: 40px;
                font-weight: 600;
                font-size: 1rem;
                transition: var(--transition);
                border: none;
                cursor: pointer;
                background: transparent;
            }
            .btn-primary {
                background: var(--bleu-pro);
                color: white;
                box-shadow: 0 8px 18px rgba(10, 49, 67, 0.15);
            }
            .btn-primary:hover {
                background: #0c3b50;
                transform: translateY(-3px);
                box-shadow: 0 15px 25px rgba(10, 49, 67, 0.2);
            }
            .btn-outline {
                border: 2px solid var(--bleu-pro);
                color: var(--bleu-pro);
                background: white;
            }
            .btn-outline:hover {
                background: var(--bleu-pro);
                color: white;
            }
            /* ---------- HEADER / NAV ---------- */
            header {
                padding: 20px 0;
                background: rgba(255,255,255,0.9);
                backdrop-filter: blur(10px);
                position: sticky;
                top: 0;
                z-index: 100;
                border-bottom: 1px solid rgba(10,49,67,0.05);
            }
            .navbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .logo {
                font-size: 1.8rem;
                font-weight: 800;
                color: var(--bleu-pro);
                letter-spacing: -0.02em;
            }
            .logo span {
                color: var(--or);
            }
            .nav-links {
                display: flex;
                gap: 48px;
                font-weight: 500;
            }
            .nav-links a {
                color: var(--gris-fonce);
                transition: var(--transition);
                font-size: 0.95rem;
            }
            .nav-links a:hover {
                color: var(--or);
            }
            .nav-cta {
                background: var(--or);
                color: white !important;
                padding: 10px 24px;
                border-radius: 30px;
                font-weight: 600;
            }
            .nav-cta:hover {
                background: #b6862e;
                color: white !important;
            }
            .mobile-menu {
                display: none;
                font-size: 1.8rem;
                color: var(--bleu-pro);
                cursor: pointer;
            }
            /* ---------- HERO SECTION ---------- */
            .hero {
                padding: 80px 0 60px;
                background: linear-gradient(145deg, #ffffff 0%, #f8fafd 100%);
            }
            .hero-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 60px;
                align-items: center;
            }
            .hero-title {
                font-size: 3.2rem;
                font-weight: 800;
                letter-spacing: -0.03em;
                color: var(--bleu-pro);
                margin-bottom: 24px;
            }
            .hero-title i {
                color: var(--or);
                font-style: normal;
            }
            .hero-sub {
                font-size: 1.3rem;
                color: var(--gris-moyen);
                margin-bottom: 32px;
                font-weight: 400;
            }
            .hero-ctas {
                display: flex;
                gap: 20px;
                margin-bottom: 40px;
            }
            .hero-stats {
                display: flex;
                gap: 40px;
            }
            .stat-item {
                display: flex;
                flex-direction: column;
            }
            .stat-number {
                font-size: 1.8rem;
                font-weight: 800;
                color: var(--bleu-pro);
            }
            .stat-label {
                font-size: 0.9rem;
                color: var(--gris-moyen);
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }
            /* ---------- CARTE INTERACTIVE (PREVIEW) ---------- */
            .carte-preview {
                background: var(--gris-clair);
                border-radius: 24px;
                padding: 8px;
                box-shadow: var(--ombre);
                height: 380px;
                position: relative;
                border: 1px solid rgba(255,255,255,0.3);
            }
            #map {
                height: 100%;
                width: 100%;
                border-radius: 20px;
                z-index: 1;
            }
            .map-badge {
                position: absolute;
                bottom: 20px;
                left: 20px;
                background: white;
                padding: 12px 20px;
                border-radius: 40px;
                font-weight: 600;
                font-size: 0.9rem;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                gap: 8px;
                z-index: 10;
            }
            .map-badge i {
                color: var(--or);
            }
            /* ---------- 3 PILIERS (TROUVER / SÉCURISER / CONTACTER) ---------- */
            .piliers {
                padding: 100px 0;
                background: white;
            }
            .piliers-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
                margin-top: 20px;
            }
            .pilier-card {
                background: white;
                border-radius: var(--border-radius);
                padding: 40px 30px;
                box-shadow: var(--ombre);
                border: 1px solid rgba(10,49,67,0.03);
                transition: var(--transition);
            }
            .pilier-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 25px 45px rgba(10,49,67,0.1);
            }
            .pilier-icon {
                font-size: 2.5rem;
                color: var(--or);
                margin-bottom: 24px;
            }
            .pilier-card h3 {
                font-size: 1.8rem;
                font-weight: 700;
                margin-bottom: 16px;
                color: var(--bleu-pro);
            }
            .pilier-card p {
                color: var(--gris-moyen);
                margin-bottom: 24px;
            }
            .pilier-link {
                font-weight: 600;
                color: var(--bleu-pro);
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border-bottom: 2px solid transparent;
                transition: var(--transition);
            }
            .pilier-link:hover {
                color: var(--or);
                gap: 12px;
            }
            /* ---------- FONCTIONNALITÉS CLÉS (ADAPTÉ RDC) ---------- */
            .features {
                padding: 80px 0;
                background: var(--gris-clair);
            }
            .features-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 40px;
            }
            .feature-item {
                display: flex;
                gap: 20px;
                align-items: flex-start;
            }
            .feature-icon {
                background: white;
                padding: 12px;
                border-radius: 16px;
                color: var(--or);
                font-size: 1.5rem;
                box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            }
            .feature-text h4 {
                font-size: 1.3rem;
                margin-bottom: 8px;
                color: var(--bleu-pro);
            }
            .feature-text p {
                color: var(--gris-moyen);
            }
            /* ---------- SPÉCIFICITÉS RDC ---------- */
            .rdc-specific {
                padding: 100px 0;
                background: white;
            }
            .rdc-badge {
                background: rgba(199, 154, 62, 0.1);
                color: var(--or);
                padding: 8px 20px;
                border-radius: 40px;
                display: inline-block;
                font-weight: 600;
                margin-bottom: 20px;
            }
            .rdc-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 60px;
                align-items: center;
            }
            .rdc-list {
                list-style: none;
            }
            .rdc-list li {
                margin-bottom: 20px;
                display: flex;
                gap: 12px;
                align-items: center;
                font-size: 1.1rem;
            }
            .rdc-list i {
                color: var(--or);
                font-size: 1.2rem;
            }
            /* ---------- TÉMOIGNAGES (PREUVE SOCIALE) ---------- */
            .testimonials {
                padding: 80px 0;
                background: var(--gris-clair);
            }
            .testimonial-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }
            .testimonial-card {
                background: white;
                padding: 40px;
                border-radius: 20px;
                box-shadow: var(--ombre);
            }
            .testimonial-text {
                font-size: 1.2rem;
                font-style: italic;
                margin-bottom: 30px;
                color: var(--gris-fonce);
            }
            .testimonial-author {
                display: flex;
                align-items: center;
                gap: 16px;
            }
            .author-avatar {
                background: var(--bleu-clair);
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 700;
            }
            .author-info h5 {
                font-weight: 700;
                margin-bottom: 4px;
            }
            .author-info p {
                color: var(--gris-moyen);
                font-size: 0.9rem;
            }
            /* ---------- CTA FINAL ---------- */
            .cta-final {
                padding: 100px 0;
                background: var(--bleu-pro);
                color: white;
                text-align: center;
            }
            .cta-final h2 {
                font-size: 2.5rem;
                margin-bottom: 20px;
            }
            .cta-final .btn-primary {
                background: var(--or);
                color: var(--bleu-pro);
                margin-top: 30px;
                font-size: 1.1rem;
                padding: 16px 48px;
            }
            .cta-final .btn-primary:hover {
                background: #e0aa4e;
            }
            /* ---------- FOOTER ---------- */
            footer {
                background: #0a1c24;
                color: white;
                padding: 60px 0 30px;
            }
            .footer-grid {
                display: grid;
                grid-template-columns: 2fr 1fr 1fr 1fr;
                gap: 60px;
                margin-bottom: 60px;
            }
            .footer-logo {
                font-size: 1.8rem;
                font-weight: 800;
                margin-bottom: 16px;
            }
            .footer-logo span {
                color: var(--or);
            }
            .footer-small {
                color: rgba(255,255,255,0.6);
                font-size: 0.9rem;
            }
            .footer-links h5 {
                margin-bottom: 24px;
                color: white;
            }
            .footer-links ul {
                list-style: none;
            }
            .footer-links li {
                margin-bottom: 12px;
            }
            .footer-links a {
                color: rgba(255,255,255,0.7);
                transition: var(--transition);
            }
            .footer-links a:hover {
                color: var(--or);
            }
            .copyright {
                text-align: center;
                padding-top: 30px;
                border-top: 1px solid rgba(255,255,255,0.1);
                color: rgba(255,255,255,0.5);
            }
            /* ---------- RESPONSIVE ---------- */
            @media (max-width: 1024px) {
                .hero-title { font-size: 2.8rem; }
                .hero-grid, .rdc-grid, .footer-grid { gap: 40px; }
            }
            @media (max-width: 900px) {
                .nav-links { 
                    display: none;
                    position: absolute;
                    top: 70px;
                    right: 20px;
                    background: white;
                    border-radius: 12px;
                    padding: 16px;
                    flex-direction: column;
                    gap: 12px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                    z-index: 50;
                }
                .nav-links.active { display: flex !important; }
                .mobile-menu { display: block; }
                .hero-grid, .piliers-grid, .features-grid, .testimonial-grid, .rdc-grid, .footer-grid {
                    grid-template-columns: 1fr;
                }
                .hero { text-align: center; }
                .hero-ctas { justify-content: center; }
                .hero-stats { justify-content: center; }
                .carte-preview { height: 300px; }
                .section-title { font-size: 2rem; }
            }
            @media (max-width: 600px) {
                .container { padding: 0 20px; }
                .hero-title { font-size: 2.2rem; }
                .btn { padding: 12px 24px; }
            }

            /* ---------- ANIMATIONS (classe fade-in pour JS) ---------- */
            .fade-in {
                opacity: 0;
                transform: translateY(25px);
                transition: opacity 0.8s ease, transform 0.8s ease;
            }
            .fade-in.visible {
                opacity: 1;
                transform: translateY(0);
            }
        </style>
    </head>
    <body>
        <!-- HEADER -->
        <header>
            <div class="container navbar">
                <div class="logo">KEL<span>FONCIA</span></div>
                <div class="nav-links">
                    <a href="connexion.php" class="nav-cta">Se connecter</a>
                </div>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </header>

        <!-- HERO SECTION -->
        <section class="hero">
            <div class="container hero-grid">
                <div class="fade-in">
                    <h1 class="hero-title">Vous cherchez un terrain <i>sécurisé</i> à <span id="rotating-province" style="display: inline-block; min-width: 200px;">Kinshasa</span> ?</h1>
                    <p class="hero-sub">KelFoncia est le premier logiciel qui centralise les opportunités foncières fiables en RDC. Identifiez, sécurisez et développez.</p>
                    <div class="hero-ctas">
                        <a href="#" class="btn btn-primary">Trouver du foncier</a>
                        <a href="#" class="btn btn-outline">Publier une opportunité</a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number">+150</span>
                            <span class="stat-label">Opportunités vérifiées</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">45+</span>
                            <span class="stat-label">Promoteurs actifs</span>
                        </div>
                    </div>
                </div>
                <div class="carte-preview fade-in">
                    <div id="map"></div>
                    <div class="map-badge">
                        <i class="fas fa-map-marker-alt"></i> Kinshasa, Gombe • 12 opportunités
                    </div>
                </div>
            </div>
        </section>

        <!-- 3 PILIERS : TROUVER / SÉCURISER / CONTACTER -->
        <section class="piliers">
            <div class="container">
                <h2 class="section-title fade-in">Le logiciel qui structure votre développement foncier</h2>
                <p class="section-sub fade-in">Inspiré de KelFoncier France, entièrement adapté aux réalités congolaises.</p>
                <div class="piliers-grid">
                    <div class="pilier-card fade-in">
                        <div class="pilier-icon"><i class="fas fa-magnifying-glass-chart"></i></div>
                        <h3>JE TROUVE</h3>
                        <p>les terrains à plus fort potentiel. Filtres par superficie, budget, usage. Carte interactive.</p>
                        <a href="#" class="pilier-link">Explorer <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="pilier-card fade-in">
                        <div class="pilier-icon"><i class="fas fa-shield"></i></div>
                        <h3>JE SÉCURISE</h3>
                        <p>Statut juridique, titre foncier, certificat. Chaque opportunité est documentée et vérifiée.</p>
                        <a href="#" class="pilier-link">Voir la fiabilité <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="pilier-card fade-in">
                        <div class="pilier-icon"><i class="fas fa-handshake"></i></div>
                        <h3>JE CONTACTE</h3>
                        <p>Messagerie interne sécurisée. Mise en relation qualifiée avec propriétaires et experts.</p>
                        <a href="#" class="pilier-link">Démarrer <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FONCTIONNALITÉS CLÉS -->
        <section class="features">
            <div class="container">
                <h2 class="section-title fade-in">Une plateforme conçue pour les professionnels</h2>
                <p class="section-sub fade-in">Centralisez, gérez, accélérez vos projets immobiliers.</p>
                <div class="features-grid">
                    <div class="feature-item fade-in">
                        <div class="feature-icon"><i class="fas fa-table-cells-large"></i></div>
                        <div class="feature-text">
                            <h4>Tableau de bord pro</h4>
                            <p>Suivi des opportunités, favoris, statistiques de visibilité de vos annonces.</p>
                        </div>
                    </div>
                    <div class="feature-item fade-in">
                        <div class="feature-icon"><i class="fas fa-message"></i></div>
                        <div class="feature-text">
                            <h4>Messagerie sécurisée</h4>
                            <p>Échanges traçables, historique, demandes de contact formalisées.</p>
                        </div>
                    </div>
                    <div class="feature-item fade-in">
                        <div class="feature-icon"><i class="fas fa-file-signature"></i></div>
                        <div class="feature-text">
                            <h4>Publication & vérification</h4>
                            <p>Ajoutez photos, documents, statut juridique. KYC léger des utilisateurs.</p>
                        </div>
                    </div>
                    <div class="feature-item fade-in">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="feature-text">
                            <h4>Filtres avancés</h4>
                            <p>Province, ville, commune, prix, usage (résidentiel, commercial, industriel).</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SPÉCIFICITÉS RDC -->
        <section class="rdc-specific">
            <div class="container">
                <div class="rdc-grid">
                    <div class="fade-in">
                        <span class="rdc-badge"><i class="fas fa-africa"></i> Conçu pour la RDC</span>
                        <h2 class="section-title" style="margin-bottom: 24px;">Le foncier congolais devient lisible</h2>
                        <ul class="rdc-list">
                            <li><i class="fas fa-check-circle"></i> Titres fonciers, certificats, droits réels… pris en compte</li>
                            <li><i class="fas fa-check-circle"></i> Experts fonciers locaux partenaires</li>
                            <li><i class="fas fa-check-circle"></i> Mode hors ligne / connexion limitée</li>
                            <li><i class="fas fa-check-circle"></i> Français · Lingala (prochainement)</li>
                            <li><i class="fas fa-check-circle"></i> Administration foncière intégrée (2026)</li>
                        </ul>
                    </div>
                    <div class="fade-in" style="background: var(--gris-clair); border-radius: 30px; padding: 40px;">
                        <i class="fas fa-map" style="font-size: 3rem; color: var(--or); margin-bottom: 20px;"></i>
                        <h3 style="color: var(--bleu-pro); margin-bottom: 16px;">Kinshasa, puis toute la RDC</h3>
                        <p style="color: var(--gris-moyen);">Déploiement pilote à Kinshasa. Extension nationale prévue en 2026.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TÉMOIGNAGES -->
        <section class="testimonials">
            <div class="container">
                <h2 class="section-title fade-in">Ils utilisent déjà KelFoncia</h2>
                <p class="section-sub fade-in">Des promoteurs et aménageurs témoignent.</p>
                <div class="testimonial-grid">
                    <div class="testimonial-card fade-in">
                        <div class="testimonial-text">« Enfin un outil qui comprend la complexité du foncier à Kinshasa. La vérisation des documents nous fait gagner des mois. »</div>
                        <div class="testimonial-author">
                            <div class="author-avatar">ML</div>
                            <div class="author-info">
                                <h5>Ag7 L.</h5>
                                <p>Promoteur immobilier, Kin Ouest</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card fade-in">
                        <div class="testimonial-text">« Je publie mes terrains et je reçois des demandes qualifiées. Plus de négociations à l'aveugle. »</div>
                        <div class="testimonial-author">
                            <div class="author-avatar">BT</div>
                            <div class="author-info">
                                <h5>Béatrice T.</h5>
                                <p>Propriétaire foncier, Gombe</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA FINAL -->
        <section class="cta-final">
            <div class="container">
                <h2 class="fade-in">Prêt à structurer votre développement foncier ?</h2>
                <p style="font-size: 1.3rem; opacity: 0.9; margin-bottom: 20px;" class="fade-in">48h d'essai gratuit, sans engagement.</p>
                <a href="actualites.php" class="btn btn-primary fade-in">Démarrer l'essai gratuit</a>
            </div>
        </section>

        <!-- FOOTER -->
        <footer>
            <div class="container">
                <div class="footer-grid">
                    <div>
                        <div class="footer-logo">KEL<span>FONCIA</span></div>
                        <p style="color: rgba(255,255,255,0.7); margin-bottom: 20px; max-width: 300px;">Le logiciel de référence du foncier professionnel en RDC et Afrique.</p>
                        <p class="footer-small">© 2026 KelFoncia. Tous droits réservés.</p>
                    </div>
                    <div class="footer-links">
                        <h5>Plateforme</h5>
                        <ul>
                            <li><a href="#">Trouver du foncier</a></li>
                            <li><a href="#">Publier</a></li>
                            <li><a href="#">Tarifs</a></li>
                            <li><a href="#">Essai gratuit</a></li>
                        </ul>
                    </div>
                    <div class="footer-links">
                        <h5>Ressources</h5>
                        <ul>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Guides fonciers RDC</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                    <div class="footer-links">
                        <h5>Légal</h5>
                        <ul>
                            <li><a href="#">CGU</a></li>
                            <li><a href="#">Confidentialité</a></li>
                            <li><a href="#">Mentions légales</a></li>
                        </ul>
                    </div>
                </div>
                <div class="copyright">
                    Conçu pour la transformation numérique du foncier en République Démocratique du Congo.
                </div>
            </div>
        </footer>

        <!-- SCRIPTS : Leaflet + animations -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="js/api.js"></script>
        <script>
        // small initializer to fetch latest listings for homepage preview
        document.addEventListener('DOMContentLoaded', async function(){
            const preview = document.querySelector('.rdc-list');
            if (!preview) return;
            const res = await KelFonciaAPI.get('listings_list');
            if (!res || !res.ok) return;
            // render first 3 items
            preview.innerHTML = '';
            res.listings.slice(0,3).forEach(l => {
                const li = document.createElement('li');
                li.innerHTML = `<i class="fas fa-map-marker-alt"></i> ${l.title} — ${l.province||''}`;
                preview.appendChild(li);
            });
        });
        </script>
        <script src="js/animations.js"></script>
        <script>
            // 1. CARTE INTERACTIVE (statique / preview)
            function initMap() {
                const map = L.map('map').setView([-4.325, 15.322], 12); // Kinshasa
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap, CartoDB',
                    subdomains: 'abcd',
                    maxZoom: 19
                }).addTo(map);
                
                // Marqueurs exemples (Kinshasa)
                const markers = [
                    { lat: -4.330, lng: 15.315, title: 'Gombe - 4500m²' },
                    { lat: -4.350, lng: 15.295, title: 'Lingwala - 1200m²' },
                    { lat: -4.380, lng: 15.340, title: 'Limete - 8000m²' }
                ];
                markers.forEach(m => {
                    L.marker([m.lat, m.lng]).addTo(map).bindPopup(m.title);
                });
                
                // Désactiver le scroll sur la carte pour éviter conflit page
                map.scrollWheelZoom.disable();
            }

            // 2. ANIMATIONS AU SCROLL (fade-in)
            function handleFadeIn() {
                const elements = document.querySelectorAll('.fade-in');
                const windowHeight = window.innerHeight;
                elements.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.top < windowHeight - 80) {
                        el.classList.add('visible');
                    }
                });
            }

            // 3. INIT
            // (Mobile menu handling moved to animations.js - no inline logic needed)

            window.onload = function() {
                initMap();
                handleFadeIn();
                window.addEventListener('scroll', handleFadeIn);
                window.addEventListener('resize', handleFadeIn);
            };
        </script>
    </body>
</html>