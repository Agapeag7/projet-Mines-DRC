<?php if (session_status() === PHP_SESSION_NONE) session_start(); $logged = !empty($_SESSION['user_id']); ?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recherche foncière • KelFoncia RDC</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <!-- HEADER (copier depuis header.php) -->
        <header>
            <div class="container navbar">
                <a href="index.php" class="logo">KEL<span>FONCIA</span></a>
                <div class="nav-links">
                    <a href="recherche.php" class="active">Trouver du foncier</a>                
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
            <!-- SECTION FILTRES -->
            <section class="search-header">
                <div class="container">
                    <h1 class="search-title fade-in">Trouvez le terrain idéal</h1>
                    
                    <div class="filtres-avances fade-in">
                        <div class="filtres-grid">
                            <div class="filtre-groupe">
                                <label>Province</label>
                                <select id="province-select">
                                    <option value="">Toutes les provinces</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Ville</label>
                                <select id="ville-select" disabled>
                                    <option value="">Toutes les villes</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Commune</label>
                                <select id="commune-select" disabled>
                                    <option value="">Toutes les communes</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Territoire</label>
                                <select id="territoire-select" disabled>
                                    <option value="">Tous les territoires</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Superficie min (m²)</label>
                                <input type="number" placeholder="Ex: 500">
                            </div>
                            <div class="filtre-groupe">
                                <label>Superficie max (m²)</label>
                                <input type="number" placeholder="Ex: 10000">
                            </div>
                            <div class="filtre-groupe">
                                <label>Budget max (USD)</label>
                                <input type="number" placeholder="Ex: 500000">
                            </div>
                            <div class="filtre-groupe">
                                <label>Usage</label>
                                <select>
                                    <option>Tous</option>
                                    <option>Résidentiel</option>
                                    <option>Commercial</option>
                                    <option>Industriel</option>
                                    <option>Mixte</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Statut juridique</label>
                                <select>
                                    <option>Tous</option>
                                    <option>Titre foncier</option>
                                    <option>Certificat d'enregistrement</option>
                                    <option>Contrat de location</option>
                                    <option>Droit coutumier</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Mots-clés</label>
                                <input type="text" placeholder="Ex: vue, rivière, viabilisé">
                            </div>
                        </div>
                        <div class="filtres-actions">
                            <button class="btn btn-outline">Réinitialiser</button>
                            <button class="btn btn-primary">Appliquer les filtres</button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- RÉSULTATS AVEC CARTE -->
            <section style="padding: 40px 0;">
                <div class="container">
                    <div class="resultats-stats fade-in">
                        <h2 style="font-size: 1.4rem; color: var(--bleu-pro);">24 opportunités foncières</h2>
                        <select style="padding: 8px 16px; border-radius: 8px; border: 1px solid #e0e6ed;">
                            <option>Trier par : Pertinence</option>
                            <option>Prix croissant</option>
                            <option>Prix décroissant</option>
                            <option>Superficie croissante</option>
                            <option>Superficie décroissante</option>
                        </select>
                    </div>

                    <div class="carte-resultats">
                        <div id="map-recherche" class="carte-mini fade-in"></div>
                        <div class="liste-terrains fade-in">
                            <!-- CARTE TERRAIN 1 -->
                            <div class="terrain-card">
                                <img src="https://via.placeholder.com/180x140" alt="Terrain" class="terrain-image">
                                <div class="terrain-infos">
                                    <h3>Terrain résidentiel - Gombe</h3>
                                    <div class="terrain-details">
                                        <span class="terrain-detail-item"><i class="fas fa-ruler-combined"></i> 4 500 m²</span>
                                        <span class="terrain-detail-item"><i class="fas fa-file-signature"></i> Titre foncier</span>
                                        <span class="terrain-detail-item"><i class="fas fa-map-pin"></i> Kinshasa, Gombe</span>
                                    </div>
                                    <span class="terrain-statut statut-verifie"><i class="fas fa-check-circle"></i> Vérifié</span>
                                </div>
                                <div class="terrain-prix">
                                    <span class="prix-valeur">$450 000</span>
                                    <span class="prix-devise">USD</span>
                                    <span class="terrain-statut statut-disponible" style="margin-top: 16px;">Disponible</span>
                                </div>
                            </div>

                            <!-- CARTE TERRAIN 2 -->
                            <div class="terrain-card">
                                <img src="https://via.placeholder.com/180x140" alt="Terrain" class="terrain-image">
                                <div class="terrain-infos">
                                    <h3>Terrain commercial - Limete</h3>
                                    <div class="terrain-details">
                                        <span class="terrain-detail-item"><i class="fas fa-ruler-combined"></i> 8 200 m²</span>
                                        <span class="terrain-detail-item"><i class="fas fa-file-signature"></i> Certificat</span>
                                        <span class="terrain-detail-item"><i class="fas fa-map-pin"></i> Kinshasa, Limete</span>
                                    </div>
                                    <span class="terrain-statut statut-verifie"><i class="fas fa-check-circle"></i> Vérifié</span>
                                </div>
                                <div class="terrain-prix">
                                    <span class="prix-valeur">$720 000</span>
                                    <span class="prix-devise">USD</span>
                                    <span class="terrain-statut statut-disponible" style="margin-top: 16px;">Disponible</span>
                                </div>
                            </div>

                            <!-- CARTE TERRAIN 3 -->
                            <div class="terrain-card">
                                <img src="https://via.placeholder.com/180x140" alt="Terrain" class="terrain-image">
                                <div class="terrain-infos">
                                    <h3>Terrain industriel - Mont Ngafula</h3>
                                    <div class="terrain-details">
                                        <span class="terrain-detail-item"><i class="fas fa-ruler-combined"></i> 12 500 m²</span>
                                        <span class="terrain-detail-item"><i class="fas fa-file-signature"></i> Titre foncier</span>
                                        <span class="terrain-detail-item"><i class="fas fa-map-pin"></i> Kinshasa, Mont Ngafula</span>
                                    </div>
                                    <span class="terrain-statut statut-verifie"><i class="fas fa-check-circle"></i> Vérifié</span>
                                </div>
                                <div class="terrain-prix">
                                    <span class="prix-valeur">$950 000</span>
                                    <span class="prix-devise">USD</span>
                                    <span class="terrain-statut statut-disponible" style="margin-top: 16px;">Disponible</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- PAGINATION SIMPLE -->
                    <div style="display: flex; justify-content: center; gap: 12px; margin-top: 40px;">
                        <span style="padding: 10px 16px; background: var(--bleu-pro); color: white; border-radius: 8px;">1</span>
                        <a href="#" style="padding: 10px 16px; background: white; border: 1px solid #e0e6ed; border-radius: 8px;">2</a>
                        <a href="#" style="padding: 10px 16px; background: white; border: 1px solid #e0e6ed; border-radius: 8px;">3</a>
                        <a href="#" style="padding: 10px 16px; background: white; border: 1px solid #e0e6ed; border-radius: 8px;">...</a>
                        <a href="#" style="padding: 10px 16px; background: white; border: 1px solid #e0e6ed; border-radius: 8px;">12</a>
                    </div>
                </div>
            </section>
        </main>

        <!-- FOOTER (copier depuis footer.php) -->
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
        <script src="js/provinces-data.js"></script>
        <script src="js/api.js"></script>
        <script src="js/actions.js"></script>
        <script src="js/animations.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const provinceSelect = document.getElementById('province-select');
            const villeSelect = document.getElementById('ville-select');
            const communeSelect = document.getElementById('commune-select');
            const territoireSelect = document.getElementById('territoire-select');

            function setOptions(select, items, placeholder) {
                select.innerHTML = '';
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = placeholder;
                select.appendChild(opt);
                items.forEach(item => {
                    const o = document.createElement('option');
                    o.value = item;
                    o.textContent = item;
                    select.appendChild(o);
                });
            }

            // initialise provinces
            setOptions(provinceSelect, getProvinces(), 'Toutes les provinces');

            provinceSelect.addEventListener('change', function () {
                const prov = this.value;
                if (!prov) {
                    setOptions(villeSelect, [], 'Toutes les villes');
                    setOptions(communeSelect, [], 'Toutes les communes');
                    setOptions(territoireSelect, [], 'Tous les territoires');
                    villeSelect.disabled = true;
                    communeSelect.disabled = true;
                    territoireSelect.disabled = true;
                    return;
                }
                const villes = getVillesByProvince(prov);
                const territoires = getTerritoiresByProvince(prov);
                setOptions(villeSelect, villes, 'Toutes les villes');
                setOptions(communeSelect, [], 'Toutes les communes');
                setOptions(territoireSelect, territoires, 'Tous les territoires');
                villeSelect.disabled = villes.length === 0;
                communeSelect.disabled = true;
                territoireSelect.disabled = territoires.length === 0;
            });

            villeSelect.addEventListener('change', function () {
                const prov = provinceSelect.value;
                const ville = this.value;
                if (!ville) {
                    setOptions(communeSelect, [], 'Toutes les communes');
                    communeSelect.disabled = true;
                    territoireSelect.disabled = getTerritoiresByProvince(prov).length === 0;
                    return;
                }
                const communes = getCommunesByVille(prov, ville);
                setOptions(communeSelect, communes, 'Toutes les communes');
                communeSelect.disabled = communes.length === 0;
                territoireSelect.disabled = false;
            });

            communeSelect.addEventListener('change', function () {
                if (this.value) {
                    territoireSelect.value = '';
                    territoireSelect.disabled = true;
                } else {
                    territoireSelect.disabled = getTerritoiresByProvince(provinceSelect.value).length === 0;
                }
            });

            // initial states
            villeSelect.disabled = true;
            communeSelect.disabled = true;
            territoireSelect.disabled = true;
        });

        // Load listings via AJAX with better UX
        document.addEventListener('DOMContentLoaded', async function(){
            const container = document.querySelector('.liste-terrains');
            const statsEl = document.querySelector('.resultats-stats h2');
            if (!container) return;

            async function renderListings(rows) {
                container.innerHTML = '';
                if (!rows || rows.length === 0) {
                    container.innerHTML = `<div style="text-align: center; padding: 60px 20px; color: var(--gris-moyen);">
                        <i class="fas fa-search" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 20px;"></i>
                        <p>Aucune annonce ne correspond à votre recherche.</p>
                        <p style="font-size: 0.9rem;">Essayez un autre critère ou consultez toutes les annonces.</p>
                    </div>`;
                    if (statsEl) statsEl.textContent = '0 opportunité foncière';
                    return;
                }
                
                rows.forEach(l => {
                    const div = document.createElement('div');
                    div.className = 'terrain-card';
                    const localPlaceholder = 'img/placeholder.png';
                    let imgSrc = localPlaceholder;
                    if (l.thumbnail_full_url) {
                        imgSrc = l.thumbnail_full_url;
                    } else if (l.thumbnail_path) {
                        imgSrc = '/KelFoncia-DRC/' + l.thumbnail_path.replace(/^\/+/, '');
                    } else if (l.thumbnail_id) {
                        imgSrc = '/KelFoncia-DRC/doc/photos/' + l.thumbnail_id;
                    }
                    const locText = [l.ville, l.province].filter(x=>x).join(', ');
                    
                    div.innerHTML = `
                        <a href="detail-terrain.php?id=${l.id}" style="text-decoration: none; color: inherit;">
                            <img src="${imgSrc}" alt="${l.title}" class="terrain-image" onerror="this.src='${localPlaceholder}'">
                        </a>
                        <div class="terrain-infos">
                            <h3><a href="detail-terrain.php?id=${l.id}" style="color: inherit; text-decoration: none;">${l.title || 'Terrain'}</a></h3>
                            <div class="terrain-details">
                                ${l.area_m2 ? `<span class="terrain-detail-item"><i class="fas fa-ruler-combined"></i> ${l.area_m2} m²</span>` : ''}
                                ${l.statut ? `<span class="terrain-detail-item"><i class="fas fa-file-signature"></i> ${l.statut}</span>` : ''}
                                ${locText ? `<span class="terrain-detail-item"><i class="fas fa-map-pin"></i> ${locText}</span>` : ''}
                            </div>
                            <span class="terrain-statut statut-verifie"><i class="fas fa-check-circle"></i> Vérifié</span>
                        </div>
                        <div class="terrain-prix">
                            <span class="prix-valeur">${l.price || 'N/A'}</span>
                            <span class="prix-devise">${l.currency || ''}</span>
                            <button class="fav-btn" data-fav-id="${l.id}" style="margin-top: 16px; background: none; border: none; cursor: pointer; color: var(--or); font-weight: 600;">
                                <i class="far fa-heart"></i> Favori
                            </button>
                        </div>`;
                    container.appendChild(div);
                });
                
                if (statsEl) statsEl.textContent = rows.length + ' opportunité' + (rows.length > 1 ? 's' : '') + ' foncière' + (rows.length > 1 ? 's' : '');
                KelActions.attachFavoriteButtons('.fav-btn');
            }

            async function load(filters={}) {
                container.innerHTML = `<div style="text-align: center; padding: 60px 20px;">
                    <div style="display: inline-block;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: var(--or);"></i>
                        <p style="margin-top: 20px; color: var(--gris-moyen);">Chargement des annonces...</p>
                    </div>
                </div>`;
                
                const res = await KelFonciaAPI.get('listings_list', filters);
                if (!res || !res.ok) {
                    container.innerHTML = `<div style="text-align: center; padding: 60px 20px; color: #dc2626;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; opacity: 0.5; display: block; margin-bottom: 20px;"></i>
                        <p>Erreur lors du chargement des annonces.</p>
                        <p style="font-size: 0.9rem; margin-top: 10px;">${res?.message || 'Veuillez réessayer.'}</p>
                    </div>`;
                    if (statsEl) statsEl.textContent = '0 opportunité foncière';
                    return;
                }
                renderListings(res.listings || []);
            }

            // initial load
            load();

            // wire filter buttons
            const applyBtn = document.querySelector('.filtres-actions .btn-primary');
            const resetBtn = document.querySelector('.filtres-actions .btn-outline');
            
            applyBtn && applyBtn.addEventListener('click', function(e){
                e.preventDefault();
                const filters = {};
                const prov = document.getElementById('province-select').value;
                const ville = document.getElementById('ville-select').value;
                const minArea = document.querySelector('input[placeholder="Ex: 500"]').value;
                const maxArea = document.querySelector('input[placeholder="Ex: 10000"]').value;
                const budget = document.querySelector('input[placeholder="Ex: 500000"]').value;
                
                if (prov) filters.province = prov;
                if (ville) filters.ville = ville;
                if (minArea) filters.min_area = minArea;
                if (maxArea) filters.max_area = maxArea;
                if (budget) filters.max_price = budget;
                
                load(filters);
            });
            
            resetBtn && resetBtn.addEventListener('click', function(e){
                e.preventDefault();
                document.querySelectorAll('.filtres-avances select, .filtres-avances input').forEach(el => el.value = '');
                document.getElementById('ville-select').disabled = true;
                document.getElementById('commune-select').disabled = true;
                document.getElementById('territoire-select').disabled = true;
                load();
            });
        });
        </script>
    </body>
</html>