<?php 
if (session_status() === PHP_SESSION_NONE) session_start(); 
$logged = !empty($_SESSION['user_id']);

// Redirect to login if not logged in
if (!$logged) {
    header('Location: connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Terrain à Gombe • 4500m² • KelFoncia RDC</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="css/style.css">
        <style>
            /* Styles supplémentaires pour la page détail */
            .detail-header {
                background: linear-gradient(145deg, #ffffff 0%, #f8fafd 100%);
                padding: 40px 0;
                border-bottom: 1px solid rgba(10,49,67,0.05);
            }
            
            .detail-title {
                font-size: 2.2rem;
                color: var(--bleu-pro);
                margin-bottom: 16px;
            }
            
            .detail-meta {
                display: flex;
                gap: 30px;
                margin-bottom: 30px;
                flex-wrap: wrap;
            }
            
            .meta-item {
                display: flex;
                align-items: center;
                gap: 10px;
                color: var(--gris-moyen);
            }
            
            .meta-item i {
                color: var(--or);
                width: 20px;
            }
            
            .detail-gallery {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 20px;
                margin-bottom: 40px;
            }
            
            .gallery-main {
                position: relative;
                width: 100%;
                height: 450px;
                background: linear-gradient(145deg, var(--gris-clair), #e8ecf0);
                border-radius: var(--border-radius-lg);
                overflow: hidden;
                cursor: pointer;
            }
            
            .gallery-main img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .gallery-main .badge {
                position: absolute;
                top: 20px;
                left: 20px;
                background: var(--or);
                color: white;
                padding: 8px 20px;
                border-radius: 30px;
                font-weight: 600;
                font-size: 0.9rem;
            }
            
            .gallery-thumbs {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .gallery-thumb {
                height: 215px;
                background: var(--gris-clair);
                border-radius: var(--border-radius);
                overflow: hidden;
                cursor: pointer;
                transition: var(--transition);
                border: 2px solid transparent;
            }
            
            .gallery-thumb:hover {
                border-color: var(--or);
                transform: scale(0.98);
            }
            
            .gallery-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .detail-infos-grid {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 40px;
                margin-bottom: 60px;
            }
            
            .detail-section {
                background: white;
                padding: 35px;
                border-radius: var(--border-radius-lg);
                box-shadow: var(--ombre);
                margin-bottom: 30px;
            }
            
            .detail-section h3 {
                font-size: 1.3rem;
                color: var(--bleu-pro);
                margin-bottom: 24px;
                display: flex;
                align-items: center;
                gap: 12px;
            }
            
            .detail-section h3 i {
                color: var(--or);
            }
            
            .caracteristiques-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }
            
            .carac-item {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            
            .carac-icon {
                width: 48px;
                height: 48px;
                background: rgba(199, 154, 62, 0.1);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--or);
                font-size: 1.3rem;
            }
            
            .carac-text {
                display: flex;
                flex-direction: column;
            }
            
            .carac-label {
                font-size: 0.85rem;
                color: var(--gris-moyen);
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }
            
            .carac-value {
                font-weight: 700;
                color: var(--bleu-pro);
                font-size: 1.1rem;
            }
            
            .prix-box {
                background: white;
                padding: 35px;
                border-radius: var(--border-radius-lg);
                box-shadow: var(--ombre-forte);
                position: sticky;
                top: 100px;
            }
            
            .prix-box .prix {
                font-size: 2.5rem;
                font-weight: 800;
                color: var(--bleu-pro);
                margin-bottom: 8px;
            }
            
            .prix-box .devise {
                color: var(--gris-moyen);
                font-size: 1.1rem;
                font-weight: 400;
                margin-left: 5px;
            }
            
            .prix-box .statut {
                display: inline-block;
                padding: 8px 20px;
                background: rgba(46, 204, 113, 0.1);
                color: #27ae60;
                border-radius: 30px;
                font-weight: 600;
                margin: 20px 0;
            }
            
            .contact-actions {
                display: flex;
                flex-direction: column;
                gap: 15px;
                margin-top: 25px;
            }
            
            .contact-actions .btn {
                width: 100%;
                padding: 16px;
            }
            
            .proprio-info {
                margin-top: 30px;
                padding-top: 30px;
                border-top: 1px solid #e0e6ed;
            }
            
            .documents-list {
                list-style: none;
                margin-top: 20px;
            }
            
            .documents-list li {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 15px 0;
                border-bottom: 1px solid #e0e6ed;
            }
            
            .documents-list li:last-child {
                border-bottom: none;
            }
            
            .documents-list i {
                color: var(--or);
                font-size: 1.2rem;
            }
            
            .documents-list a {
                color: var(--bleu-pro);
                text-decoration: none;
                font-weight: 500;
            }
            
            .documents-list a:hover {
                color: var(--or);
            }
            
            .localisation-detail {
                height: 400px;
                border-radius: var(--border-radius-lg);
                overflow: hidden;
                margin-top: 20px;
            }
            
            @media (max-width: 900px) {
                .detail-gallery {
                    grid-template-columns: 1fr;
                }
                .gallery-thumbs {
                    grid-template-columns: repeat(4, 1fr);
                }
                .gallery-thumb {
                    height: 100px;
                }
                .detail-infos-grid {
                    grid-template-columns: 1fr;
                }
                .prix-box {
                    position: static;
                }
            }
            
            @media (max-width: 600px) {
                .gallery-thumbs {
                    grid-template-columns: repeat(2, 1fr);
                }
                .caracteristiques-grid {
                    grid-template-columns: 1fr;
                }
                .detail-title {
                    font-size: 1.8rem;
                }
            }
        </style>
    </head>
    <body>
        <header>
            <div class="container navbar">
                <a href="index.php" class="logo">KEL<span>FONCIA</span></a>
                <div class="nav-links">
                    <a href="recherche.php">Trouver du foncier</a>
                    <a href="actualites.php">Fil d'actualités</a>
                    <a href="publier.php">Publier</a>
                    <?php if($logged): ?>
                        <a href="tableau-de-bord.php">Tableau de bord</a>
                        <a href="deconnexion.php">Déconnexion</a>
                    <?php else: ?>
                        <a href="connexion.php" class="nav-cta">Se connecter</a>
                    <?php endif; ?>
                </div>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </header>

        <main>
            <!-- HEADER DÉTAIL -->
            <section class="detail-header">
                <div class="container">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <nav style="display: flex; gap: 10px; color: var(--gris-moyen);">
                            <a href="recherche.php" style="color: var(--gris-moyen);">Recherche</a>
                            <span><i class="fas fa-chevron-right" style="font-size: 0.8rem;"></i></span>
                            <span style="color: var(--bleu-pro); font-weight: 500;" id="breadcrumb-title">Terrain</span>
                        </nav>
                        <div style="display: flex; gap: 16px;">
                            <button class="btn btn-outline" style="padding: 10px 20px;">
                                <i class="far fa-heart"></i> Favoris
                            </button>
                            <button class="btn btn-outline" style="padding: 10px 20px;">
                                <i class="fas fa-share-alt"></i> Partager
                            </button>
                        </div>
                    </div>
                    
                    <h1 class="detail-title fade-in">Terrain résidentiel de standing - Gombe</h1>
                    
                    <div class="detail-meta fade-in">
                        <span class="meta-item"><i class="fas fa-map-pin"></i> Kinshasa, Commune de la Gombe</span>
                        <span class="meta-item"><i class="fas fa-calendar"></i> Publié le 10/02/2026</span>
                        <span class="meta-item"><i class="fas fa-eye"></i> 145 vues</span>
                        <span class="meta-item"><i class="fas fa-shield-alt"></i> Annonce vérifiée</span>
                    </div>
                </div>
            </section>

            <section style="padding: 40px 0;">
                <div class="container">
                    <!-- GALERIE PHOTOS -->
                    <div class="detail-gallery fade-in">
                        <div class="gallery-main">
                            <span class="badge">Opportunité certifiée</span>
                            <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Terrain vue aérienne">
                        </div>
                        <div class="gallery-thumbs"></div>
                    </div>

                    <!-- GRILLE INFOS PRINCIPALES -->
                    <div class="detail-infos-grid">
                        <!-- COLONNE GAUCHE : CARACTÉRISTIQUES DÉTAILLÉES -->
                        <div>
                            <!-- SECTION CARACTÉRISTIQUES -->
                            <div class="detail-section fade-in">
                                <h3><i class="fas fa-ruler-combined"></i> Caractéristiques du terrain</h3>
                                <div class="caracteristiques-grid">
                                    <div class="carac-item">
                                        <div class="carac-icon"><i class="fas fa-vector-square"></i></div>
                                        <div class="carac-text">
                                            <span class="carac-label">Superficie</span>
                                            <span class="carac-value">4 500 m²</span>
                                        </div>
                                    </div>
                                    <div class="carac-item">
                                        <div class="carac-icon"><i class="fas fa-file-signature"></i></div>
                                        <div class="carac-text">
                                            <span class="carac-label">Statut juridique</span>
                                            <span class="carac-value">Titre foncier</span>
                                        </div>
                                    </div>
                                    <div class="carac-item">
                                        <div class="carac-icon"><i class="fas fa-building"></i></div>
                                        <div class="carac-text">
                                            <span class="carac-label">Usage</span>
                                            <span class="carac-value">Résidentiel</span>
                                        </div>
                                    </div>
                                    <div class="carac-item">
                                        <div class="carac-icon"><i class="fas fa-water"></i></div>
                                        <div class="carac-text">
                                            <span class="carac-label">Viabilisation</span>
                                            <span class="carac-value">Eau, électricité, voirie</span>
                                        </div>
                                    </div>
                                    <div class="carac-item">
                                        <div class="carac-icon"><i class="fas fa-gavel"></i></div>
                                        <div class="carac-text">
                                            <span class="carac-label">Référence titre</span>
                                            <span class="carac-value">TF-45678/KIN/2025</span>
                                        </div>
                                    </div>
                                    <div class="carac-item">
                                        <div class="carac-icon"><i class="fas fa-calendar-alt"></i></div>
                                        <div class="carac-text">
                                            <span class="carac-label">Année acquisition</span>
                                            <span class="carac-value">2020</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION DESCRIPTION -->
                            <div class="detail-section fade-in">
                                <h3><i class="fas fa-align-left"></i> Description</h3>
                                <p style="margin-bottom: 20px; color: var(--gris-fonce); line-height: 1.7;">
                                    Exceptionnel terrain situé au cœur de la Gombe, quartier résidentiel le plus prisé de Kinshasa. 
                                    Parfaitement viabilisé (eau, électricité, fibre optique), avec accès direct sur avenue bitumée.
                                </p>
                                <p style="margin-bottom: 20px; color: var(--gris-fonce); line-height: 1.7;">
                                    <strong>Atouts :</strong> Vue dégagée, proximité des ambassades et écoles internationales, 
                                    terrain plat et constructible immédiatement. Idéal pour projet immobilier de standing 
                                    (immeuble R+5 ou villa de luxe).
                                </p>
                                <p style="color: var(--gris-moyen); font-style: italic;">
                                    Certificat d'urbanisme obtenu - COS/CES disponibles sur demande.
                                </p>
                            </div>

                            <!-- SECTION LOCALISATION -->
                            <div class="detail-section fade-in">
                                <h3><i class="fas fa-map-location-dot"></i> Localisation précise</h3>
                                <div id="map-detail" class="localisation-detail"></div>
                                <div style="margin-top: 20px; display: flex; gap: 20px;">
                                    <span style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-latitude" style="color: var(--or);"></i> Lat: -4.3300
                                    </span>
                                    <span style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-longitude" style="color: var(--or);"></i> Lng: 15.3150
                                    </span>
                                    <a href="#" style="color: var(--or); margin-left: auto;">
                                        Voir en grand <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- SECTION DOCUMENTS -->
                            <div class="detail-section fade-in">
                                <h3><i class="fas fa-file-pdf"></i> Documents juridiques</h3>
                                <ul class="documents-list"></ul>
                            </div>
                        </div>

                        <!-- COLONNE DROITE : PRIX ET CONTACT -->
                        <div>
                            <div class="prix-box fade-in">
                                <span class="statut"><i class="fas fa-circle" style="font-size: 0.6rem; margin-right: 6px;"></i> Disponible immédiatement</span>
                                
                                <div style="margin-bottom: 20px;">
                                    <span class="prix">$450 000</span>
                                    <span class="devise">USD</span>
                                </div>
                                
                                <div style="display: flex; gap: 16px; margin-bottom: 25px;">
                                    <span style="display: flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-chart-line" style="color: #27ae60;"></i>
                                        <span style="font-weight: 600;">$100/m²</span>
                                    </span>
                                    <span style="display: flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-clock" style="color: var(--or);"></i>
                                        <span>Négociable</span>
                                    </span>
                                </div>

                                <div class="contact-actions">
                                    <button class="btn btn-primary" style="background: var(--or); color: var(--bleu-pro);">
                                        <i class="fas fa-envelope"></i> Contacter le propriétaire
                                    </button>
                                    <button class="btn btn-outline">
                                        <i class="fas fa-file-signature"></i> Faire une offre
                                    </button>
                                    <button class="btn btn-outline" style="border-color: var(--gris-moyen); color: var(--gris-moyen);">
                                        <i class="fas fa-phone-alt"></i> Voir le téléphone
                                    </button>
                                </div>

                                <div class="proprio-info"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION PROPOSITIONS SIMILAIRES -->
            <section style="padding: 60px 0; background: var(--gris-clair);">
                <div class="container">
                    <h2 style="font-size: 1.8rem; color: var(--bleu-pro); margin-bottom: 40px;">Opportunités similaires</h2>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;"></div>
                </div>
            </section>
        </main>

        <footer>
            <div class="container">
                <div class="footer-grid">
                    <div>
                        <a href="index.php" class="footer-logo">KEL<span>FONCIA</span></a>
                        <p style="color: rgba(255,255,255,0.7); margin-bottom: 20px; max-width: 300px;">Le logiciel de référence du foncier professionnel en RDC et Afrique.</p>
                        <p class="footer-small">© 2026 KelFoncia. Tous droits réservés.</p>
                    </div>
                    <div class="footer-links">
                        <h5>Plateforme</h5>
                        <ul>
                            <li><a href="recherche.php">Trouver du foncier</a></li>
                            <li><a href="publier.php">Publier</a></li>
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

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="js/animations.js"></script>
        <script src="js/api.js"></script>
        <script src="js/actions.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', async function(){
            const params = new URLSearchParams(window.location.search);
            const id = params.get('id');
            
            if (!id) {
                showDetailError('Annonce introuvable', 'Veuillez sélectionner une annonce depuis la recherche.');
                return;
            }

            function showDetailError(title, msg) {
                const container = document.querySelector('main section:nth-child(2)');
                if (container) {
                    container.innerHTML = `<div class="container" style="text-align: center; padding: 60px 20px;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #dc2626; opacity: 0.7; display: block; margin-bottom: 20px;"></i>
                        <h2 style="color: #dc2626; margin-bottom: 8px;">${title}</h2>
                        <p style="color: var(--gris-moyen); margin-bottom: 30px;">${msg}</p>
                        <a href="recherche.php" class="btn btn-primary">← Retour à la recherche</a>
                    </div>`;
                }
            }

            // Charger les détails du listing
            const res = await KelFonciaAPI.get('listings_get', { id });
            if (!res || !res.ok || !res.listing) {
                showDetailError('Annonce introuvable', 'L\'annonce que vous recherchez n\'existe pas ou a été supprimée.');
                return;
            }

            const l = res.listing;

            // ===== UPDATE PAGE TITLE & HEADER =====
            document.title = (l.title ? l.title + ' • KelFoncia RDC' : document.title);

            const titleEl = document.querySelector('.detail-title');
            if (titleEl) titleEl.textContent = l.title || 'Terrain';

            const breadcrumbTitle = document.getElementById('breadcrumb-title');
            if (breadcrumbTitle) breadcrumbTitle.textContent = l.title || 'Terrain';

            const metaItems = document.querySelectorAll('.detail-meta .meta-item');
            if (metaItems && metaItems.length > 0) {
                const locText = [l.ville, l.province].filter(x=>x).join(', ');
                if (metaItems[0]) metaItems[0].innerHTML = '<i class="fas fa-map-pin"></i> ' + (locText || 'Location non spécifiée');
                if (metaItems[1]) metaItems[1].innerHTML = '<i class="fas fa-calendar"></i> Publié le ' + (l.created_at ? l.created_at.split(' ')[0] : 'N/A');
                if (metaItems[2]) metaItems[2].innerHTML = '<i class="fas fa-eye"></i> ' + (l.view_count || 0) + ' vues';
            }

            // ===== UPDATE GALLERY =====
            const mainImg = document.querySelector('.gallery-main img');
            if (mainImg) {
                let imgSrc = '';
                if (l.thumbnail_full_url) {
                    imgSrc = l.thumbnail_full_url;
                } else if (l.thumbnail_path) {
                    imgSrc = '/KelFoncia-DRC/' + l.thumbnail_path.replace(/^\/+/, '');
                } else if (l.thumbnail_id) {
                    imgSrc = '/KelFoncia-DRC/doc/photos/' + l.thumbnail_id;
                }
                
                if (imgSrc) {
                    mainImg.src = imgSrc;
                    mainImg.onerror = function() { 
                        this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23f0f4f8" width="400" height="300"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="16" fill="%23999"%3EImage non disponible%3C/text%3E%3C/svg%3E';
                        this.onerror = null;
                    };
                }
            }

            // Charger les medias liés au listing pour les thumbnails
            const galleryThumbs = document.querySelector('.gallery-thumbs');
            if (galleryThumbs && res.medias && res.medias.length > 0) {
                galleryThumbs.innerHTML = '';
                res.medias.slice(0, 3).forEach((media, idx) => {
                    const thumb = document.createElement('div');
                    thumb.className = 'gallery-thumb';
                    const mediaUrl = media.path ? '/KelFoncia-DRC/' + media.path.replace(/^\/+/, '') : `data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="150"%3E%3Crect fill="%23f0f4f8" width="200" height="150"/%3E%3C/svg%3E`;
                    thumb.innerHTML = `<img src="${mediaUrl}" alt="Photo ${idx+1}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22150%22%3E%3Crect fill=%22%23f0f4f8%22 width=%22200%22 height=%22150%22/%3E%3C/svg%3E'">`;
                    galleryThumbs.appendChild(thumb);
                });

                // Ajouter compteur si plus de 3 images
                if (res.medias.length > 3) {
                    const moreThumb = document.createElement('div');
                    moreThumb.className = 'gallery-thumb';
                    moreThumb.innerHTML = `<div style="width: 100%; height: 100%; background: var(--bleu-pro); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">+${res.medias.length - 3}</div>`;
                    galleryThumbs.appendChild(moreThumb);
                }
            }

            // ===== UPDATE CHARACTERISTICS =====
            const caracItems = document.querySelectorAll('.carac-item');
            caracItems.forEach(item => {
                const label = item.querySelector('.carac-label')?.textContent.toLowerCase() || '';
                const valueEl = item.querySelector('.carac-value');
                if (!valueEl) return;

                let value = '';
                if (label.includes('superficie')) value = (l.area_m2 ? l.area_m2 + ' m²' : 'N/A');
                else if (label.includes('statut') || label.includes('juridique')) value = l.statut || 'N/A';
                else if (label.includes('usage')) value = (l.features?.usage || 'N/A');
                else if (label.includes('viab')) value = (l.features?.viabilisation || 'N/A');
                else if (label.includes('référence')) value = (l.features?.reference_titre || 'N/A');
                else if (label.includes('année') || label.includes('acquisition')) value = (l.features?.annee_acquisition || 'N/A');

                if (value) valueEl.textContent = value;
            });

            // ===== UPDATE DESCRIPTION =====
            const descEl = document.querySelector('.detail-section:nth-child(2) p');
            if (descEl && l.description) {
                descEl.textContent = l.description;
            }

            // ===== UPDATE MAP =====
            if (l.latitude && l.longitude) {
                const mapContainer = document.getElementById('map-detail');
                if (mapContainer && typeof initMap === 'function') {
                    if (!mapContainer._leaflet_map) {
                        initMap('map-detail', parseFloat(l.latitude), parseFloat(l.longitude), 16, [
                            { lat: parseFloat(l.latitude), lng: parseFloat(l.longitude), title: l.title }
                        ]);
                    }
                }
                const latEl = document.querySelector('.localisation-detail').parentElement.querySelector('span:nth-child(1)');
                const lngEl = document.querySelector('.localisation-detail').parentElement.querySelector('span:nth-child(2)');
                if (latEl) latEl.innerHTML = `<i class="fas fa-latitude" style="color: var(--or);"></i> Lat: ${l.latitude}`;
                if (lngEl) lngEl.innerHTML = `<i class="fas fa-longitude" style="color: var(--or);"></i> Lng: ${l.longitude}`;
            }

            // ===== UPDATE DOCUMENTS =====
            const docList = document.querySelector('.documents-list');
            if (docList) {
                if (res.medias && res.medias.filter(m => m.type === 'document').length > 0) {
                    docList.innerHTML = '';
                    res.medias.filter(m => m.type === 'document').forEach(doc => {
                        const li = document.createElement('li');
                        const icon = doc.mime_type?.includes('pdf') ? 'fa-file-pdf' : 'fa-file-image';
                        const color = doc.mime_type?.includes('pdf') ? '#e74c3c' : '#3498db';
                        li.innerHTML = `
                            <i class="fas ${icon}" style="color: ${color};"></i>
                            <div style="flex: 1;">
                                <a href="#">${doc.caption || doc.filename || 'Document'}</a>
                                <span style="display: block; color: var(--gris-moyen); font-size: 0.85rem;">${doc.size_bytes ? (Math.round(doc.size_bytes / 1024 / 1024 * 10) / 10) + ' Mo' : 'N/A'}</span>
                            </div>
                            <i class="fas fa-download" style="color: var(--gris-moyen); cursor: pointer;"></i>
                        `;
                        docList.appendChild(li);
                    });
                }
            }

            // ===== UPDATE PRICE & CURRENCY =====
            const priceNode = document.querySelector('.prix');
            if (priceNode) {
                priceNode.textContent = l.price ? '$' + new Intl.NumberFormat('en-US').format(l.price) : 'Prix sur demande';
            }
            const deviseNode = document.querySelector('.devise');
            if (deviseNode) {
                deviseNode.textContent = l.currency || 'USD';
            }

            // ===== UPDATE OWNER INFO =====
            if (res.owner) {
                const ownerInfo = document.querySelector('.proprio-info');
                if (ownerInfo) {
                    const initials = (res.owner.display_name || res.owner.email).split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
                    ownerInfo.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                            <div style="width: 60px; height: 60px; background: var(--bleu-clair); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.3rem;">
                                ${initials}
                            </div>
                            <div>
                                <h4 style="color: var(--bleu-pro); margin-bottom: 4px;">${res.owner.display_name || res.owner.email}</h4>
                                <span style="color: var(--gris-moyen); font-size: 0.9rem;">Membre depuis ${new Date(res.owner.created_at).getFullYear()}</span>
                                ${res.owner.kyc_status === 'verified' ? '<span style="display: flex; align-items: center; gap: 4px; color: #27ae60; font-size: 0.85rem; margin-top: 4px;"><i class="fas fa-check-circle"></i> Identité vérifiée</span>' : ''}
                            </div>
                        </div>
                        <div style="background: rgba(199, 154, 62, 0.05); padding: 16px; border-radius: 12px;">
                            <p style="color: var(--gris-moyen); font-size: 0.95rem;">
                                <i class="fas fa-shield-alt" style="color: var(--or); margin-right: 8px;"></i>
                                Toutes les discussions sont sécurisées et tracées. Ne communiquez jamais hors plateforme.
                            </p>
                        </div>
                    `;
                }
            }

            // ===== LOAD SIMILAR LISTINGS =====
            const similarRes = await KelFonciaAPI.get('listings_list', { 
                province: l.province, 
                limit: 3 
            });

            const similarSection = document.querySelector('section:last-of-type .container div[style*="grid"]');
            if (similarSection && similarRes && similarRes.listings) {
                similarSection.innerHTML = '';
                similarRes.listings.filter(item => item.id !== id).slice(0, 3).forEach(similar => {
                    const card = document.createElement('div');
                    card.className = 'terrain-card fade-in';
                    card.style.display = 'block';
                    card.style.background = 'white';
                    
                    let imgSrc = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="500" height="180"%3E%3Crect fill="%23f0f4f8" width="500" height="180"/%3E%3C/svg%3E';
                    if (similar.thumbnail_full_url) {
                        imgSrc = similar.thumbnail_full_url;
                    } else if (similar.thumbnail_path) {
                        imgSrc = '/KelFoncia-DRC/' + similar.thumbnail_path.replace(/^\/+/, '');
                    }

                    card.innerHTML = `
                        <img src="${imgSrc}" style="width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 16px; onerror='this.src=&quot;data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22500%22 height=%22180%22%3E%3Crect fill=%22%23f0f4f8%22 width=%22500%22 height=%22180%22/%3E%3C/svg%3E&quot;'">
                        <div style="padding: 0 16px 16px;">
                            <h3 style="font-size: 1.2rem; margin-bottom: 8px;">${similar.title || 'Terrain'}</h3>
                            <div style="display: flex; gap: 16px; margin-bottom: 12px;">
                                <span style="display: flex; align-items: center; gap: 4px; font-size: 0.9rem;"><i class="fas fa-ruler-combined" style="color: var(--or);"></i> ${similar.area_m2 || 'N/A'} m²</span>
                                <span style="display: flex; align-items: center; gap: 4px; font-size: 0.9rem;"><i class="fas fa-tag" style="color: var(--or);"></i> $${similar.price ? new Intl.NumberFormat('en-US').format(similar.price) : 'N/A'}</span>
                            </div>
                            <a href="detail-terrain.php?id=${similar.id}" style="color: var(--or); font-weight: 600;">Voir le détail <i class="fas fa-arrow-right"></i></a>
                        </div>
                    `;
                    similarSection.appendChild(card);
                });
            }

            // ===== WIRE CONTACT BUTTON =====
            const contactBtn = document.querySelector('.contact-actions .btn-primary');
            if (contactBtn && res.owner) {
                contactBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    if (!window.__loggedIn && !document.querySelector('[data-user-id]')) {
                        KelActions.showToast('Vous devez être connecté pour contacter le propriétaire', 'error');
                        setTimeout(() => window.location.href = 'connexion.php', 1000);
                    } else {
                        KelActions.openContactModal(
                            res.owner.id,
                            res.owner.display_name || res.owner.email,
                            res.owner.email,
                            id,
                            l.title
                        );
                    }
                });
            }

            // ===== WIRE FAVORITE BUTTON =====
            const favBtn = document.querySelector('.detail-header button:first-child');
            if (favBtn) {
                favBtn.addEventListener('click', async function(e){
                    e.preventDefault();
                    const res = await KelActions.toggleFavorite(id);
                    if (res && res.ok) {
                        const icon = favBtn.querySelector('i');
                        const action = res.result?.action;
                        if (action === 'added') {
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                            favBtn.style.color = 'var(--or)';
                        } else {
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                            favBtn.style.color = 'inherit';
                        }
                    }
                });
            }
        });
        </script>
    </body>
</html>