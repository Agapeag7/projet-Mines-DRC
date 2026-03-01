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
                    <a href="actualites.php">Fil d'actualités</a>
                    <a href="publier.php">Publier</a>
                    <a href="tableau-de-bord.php">Tableau de bord</a>
                    <a href="#">Tarifs</a>
                    <a href="connexion.php" class="nav-cta">Se connecter</a>
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
        <script>
        document.addEventListener('DOMContentLoaded', async function(){
            const container = document.getElementById('feed-list');
            if (!container) return;
            const res = await KelFonciaAPI.get('listings_list');
            if (!res || !res.ok) { container.innerHTML = '<p>Erreur de chargement.</p>'; return; }
            container.innerHTML = '';
            res.listings.forEach(l => {
                const card = document.createElement('div');
                card.className = 'terrain-card';
                card.innerHTML = `<h3>${l.title}</h3><p>${l.description ? l.description.substring(0,200):''}</p><a href="detail-terrain.php?id=${l.id}">Voir</a>`;
                container.appendChild(card);
            });
        });
        </script>
        <script src="js/animations.js"></script>
    </body>
</html>