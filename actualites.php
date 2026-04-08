<?php if (session_status() === PHP_SESSION_NONE) session_start(); $logged = !empty($_SESSION['user_id']); ?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Fil d'actualités • KelFoncia RDC</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <!-- HEADER -->
        <header>
            <div class="container navbar">
                <a href="index.php" class="logo">KEL<span>FONCIA</span></a>
                <div class="nav-links">
                    <a href="recherche.php">Trouver du foncier</a>
                    <a href="actualites.php" class="active">Fil d'actualités</a>
                    <a href="publier.php">Publier</a>
                    <?php if($logged): ?>
                        <a href="tableau-de-bord.php">Tableau de bord</a>
                        <a href="deconnexion.php" class="nav-cta">Déconnexion</a>
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
            <section style="padding: 60px 0;">
                <div class="container">
                    <h1 class="section-title fade-in">Fil d'actualités</h1>
                    <!-- subtitle removed as requested -->
                    
                    <div id="feed-list" class="liste-terrains">
                        <!-- Cards seront ajoutés dynamiquement par JavaScript -->
                    </div>
                    <!-- modal palette pour détails terrain -->
                    <div id="detail-modal">
                        <div class="modal-content">
                            <span class="modal-close">&times;</span>
                            <div id="modal-body"></div>
                        </div>
                    </div>
                    <div style="text-align:center; margin-top:30px;">
                        <button id="load-more" class="btn btn-outline">Voir plus</button>
                    </div>
                </div>
            </section>
        </main>

        <!-- scripts -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="js/api.js"></script>
        <script src="js/actions.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', async function(){
            const container = document.getElementById('feed-list');
            const loadMore = document.getElementById('load-more');
            let page = 1;
            const perPage = 8;

            async function loadPage() {
                container.innerHTML = `<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin" style="font-size:2rem;color:var(--or);"></i></div>`;
                const res = await KelFonciaAPI.get('listings_list', { limit: perPage, offset: (page-1)*perPage });
                if (!res || !res.ok) {
                    container.innerHTML = '<p>Erreur de chargement.</p>';
                    return;
                }
                if (page === 1) container.innerHTML = '';
                if (!res.listings || res.listings.length === 0) {
                    if (page === 1) container.innerHTML = '<p>Aucun contenu disponible pour le moment.</p>';
                    loadMore.style.display = 'none';
                    return;
                }
                res.listings.forEach(l => {
                    const card = document.createElement('div');
                    card.className = 'terrain-card';
                    const localPlaceholder = 'img/placeholder.png';
                    let imgSrc = localPlaceholder;
                    if (l.thumbnail_full_url) {
                        imgSrc = l.thumbnail_full_url;
                    } else if (l.thumbnail_path) {
                        imgSrc = '/KelFoncia-DRC/' + l.thumbnail_path.replace(/^\/+/, '');
                    } else if (l.thumbnail_id) {
                        imgSrc = '/KelFoncia-DRC/doc/photos/' + l.thumbnail_id;
                    }
                    const loc = [l.ville, l.province].filter(x=>x).join(', ');
                    card.innerHTML = `
                        <img src="${imgSrc}" class="terrain-image" onerror="this.src='${localPlaceholder}'">
                        <div class="terrain-infos">
                            <h3><a href="detail-terrain.php?id=${l.id}" style="color:inherit;text-decoration:none;">${l.title || 'Actualité'}</a></h3>
                            <p>${l.description?l.description.substring(0,150):''}</p>
                            <div class="terrain-details">
                                ${loc?`<span class="terrain-detail-item"><i class="fas fa-map-pin"></i> ${loc}</span>`:''}
                            </div>
                        </div>
                        <div class="terrain-prix">
                            <span class="prix-valeur">${l.price||''}</span>
                            <span class="prix-devise">${l.currency||''}</span>
                        </div>`;
                    container.appendChild(card);
                });
                KelActions.attachFavoriteButtons('.fav-btn');
                if (res.listings.length < perPage) loadMore.style.display = 'none';
            }

            loadMore && loadMore.addEventListener('click', function(e){
                e.preventDefault();
                page++;
                loadPage();
            });

            loadPage();
        });
        </script>
        <script src="js/animations.js"></script>
    </body>
</html>