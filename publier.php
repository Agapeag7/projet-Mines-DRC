<?php if (session_status() === PHP_SESSION_NONE) session_start(); $logged = !empty($_SESSION['user_id']); ?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Publier une opportunité • KelFoncia RDC</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <header>
            <div class="container navbar">
                <a href="index.php" class="logo">KEL<span>FONCIA</span></a>
                <div class="nav-links">
                    <a href="recherche.php">Trouver du foncier</a>
                    <a href="actualites.php">Fil d'actualités</a>
                    <a href="publier.php" class="active">Publier</a>
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

        <main style="padding: 60px 0; background: var(--gris-clair);">
            <div class="container">
                <div style="text-align: center; margin-bottom: 40px;">
                    <h1 class="section-title fade-in">Publier une opportunité foncière</h1>
                    <p class="section-sub fade-in" style="margin-left: auto; margin-right: auto;">
                        Mettez en valeur votre terrain et entrez en relation avec des promoteurs qualifiés
                    </p>
                </div>

                <!-- SECTION RECHERCHE ANNONCES EXISTANTES -->
                <section style="margin-bottom: 60px; background: white; padding: 40px; border-radius: 16px;">
                    <h2 style="font-size: 1.5rem; color: var(--bleu-pro); margin-bottom: 30px; text-align: center;">
                        <i class="fas fa-search" style="color: var(--or); margin-right: 12px;"></i>Voir les terrains existants
                    </h2>
                    
                    <div class="filtres-avances fade-in" style="margin-bottom: 30px;">
                        <div class="filtres-grid">
                            <div class="filtre-groupe">
                                <label>Province</label>
                                <select id="province-select-search">
                                    <option value="">Toutes les provinces</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Ville</label>
                                <select id="ville-select-search" disabled>
                                    <option value="">Toutes les villes</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Commune</label>
                                <select id="commune-select-search" disabled>
                                    <option value="">Toutes les communes</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Territoire</label>
                                <select id="territoire-select-search" disabled>
                                    <option value="">Tous les territoires</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Superficie min (m²)</label>
                                <input type="number" id="min-area-search" placeholder="Ex: 500">
                            </div>
                            <div class="filtre-groupe">
                                <label>Superficie max (m²)</label>
                                <input type="number" id="max-area-search" placeholder="Ex: 10000">
                            </div>
                            <div class="filtre-groupe">
                                <label>Budget max (USD)</label>
                                <input type="number" id="budget-search" placeholder="Ex: 500000">
                            </div>
                            <div class="filtre-groupe">
                                <label>Usage</label>
                                <select id="usage-search">
                                    <option value="">Tous</option>
                                    <option value="residentiel">Résidentiel</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="industriel">Industriel</option>
                                    <option value="mixte">Mixte</option>
                                    <option value="agricole">Agricole</option>
                                </select>
                            </div>
                            <div class="filtre-groupe">
                                <label>Statut juridique</label>
                                <select id="statut-search">
                                    <option value="">Tous</option>
                                    <option value="titre_foncier">Titre foncier</option>
                                    <option value="certificat">Certificat d'enregistrement</option>
                                    <option value="contrat_location">Contrat de location</option>
                                    <option value="droit_coutumier">Droit coutumier</option>
                                </select>
                            </div>
                        </div>
                        <div class="filtres-actions">
                            <button class="btn btn-outline" id="reset-search-btn">Réinitialiser</button>
                            <button class="btn btn-primary" id="apply-search-btn">Appliquer les filtres</button>
                        </div>
                    </div>

                    <div class="resultats-stats fade-in" style="margin-bottom: 30px;">
                        <h3 style="font-size: 1.1rem; color: var(--bleu-pro); margin-bottom: 15px;" id="search-count">Chargement des annonces...</h3>
                        <select id="sort-search" style="padding: 8px 16px; border-radius: 8px; border: 1px solid #e0e6ed;">
                            <option value="">Trier par : Pertinence</option>
                            <option value="price-asc">Prix croissant</option>
                            <option value="price-desc">Prix décroissant</option>
                            <option value="area-asc">Superficie croissante</option>
                            <option value="area-desc">Superficie décroissante</option>
                        </select>
                    </div>

                    <div class="liste-terrains fade-in" id="search-listings" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                        <div style="text-align: center; padding: 60px 20px; grid-column: 1 / -1;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--or); margin-bottom: 15px; display: block;"></i>
                            <p style="color: var(--gris-moyen);">Chargement des annonces...</p>
                        </div>
                    </div>
                </section>

                <form class="publier-form fade-in" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="is_published" id="is_published" value="1">
                    <!-- SECTION 1 : LOCALISATION -->
                    <div class="form-section">
                        <h3><i class="fas fa-map-marker-alt" style="color: var(--or); margin-right: 12px;"></i> Localisation</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Province *</label>
                                <select id="province-select" name="province" required>
                                    <option value="">Sélectionnez une province</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ville *</label>
                                <select id="ville-select" name="ville" required>
                                    <option value="">Sélectionnez d'abord une province</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Commune / Quartier *</label>
                                <select id="commune-select" name="commune" required>
                                    <option value="">Sélectionnez d'abord une ville</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Territoire (optionnel)</label>
                                <select id="territoire-select" name="territoire">
                                    <option value="">Aucun territoire</option>
                                </select>
                            </div>
                                <div class="form-group full-width">
                                    <label>Adresse ou lieu-dit *</label>
                                    <input name="address_text" type="text" placeholder="Ex: Avenue du Commerce, n°45" required>
                                </div>
                                <div class="form-group">
                                    <label>Latitude (optionnel)</label>
                                    <input name="latitude" type="text" placeholder="-4.3300">
                                </div>
                                <div class="form-group">
                                    <label>Longitude (optionnel)</label>
                                    <input name="longitude" type="text" placeholder="15.3150">
                                </div>
                        </div>
                    </div>

                    <!-- SECTION 2 : CARACTÉRISTIQUES -->
                    <div class="form-section">
                        <h3><i class="fas fa-ruler-combined" style="color: var(--or); margin-right: 12px;"></i> Caractéristiques</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Superficie (m²) *</label>
                                <input name="area_m2" type="number" placeholder="Ex: 5000" required>
                            </div>
                            <div class="form-group">
                                <label>Usage principal *</label>
                                <select name="usage" required>
                                    <option value="">Sélectionnez un usage</option>
                                    <option value="residentiel">Résidentiel</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="industriel">Industriel</option>
                                    <option value="mixte">Mixte</option>
                                    <option value="agricole">Agricole</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Prix (USD) *</label>
                                <input name="price" type="number" placeholder="Ex: 450000" required>
                            </div>
                            <div class="form-group">
                                <label>Statut juridique *</label>
                                <select name="statut" required>
                                    <option value="">Sélectionnez le statut</option>
                                    <option value="titre_foncier">Titre foncier</option>
                                    <option value="certificat">Certificat d'enregistrement</option>
                                    <option value="contrat_location">Contrat de location longue durée</option>
                                    <option value="droit_superficie">Droit de superficie</option>
                                    <option value="droit_coutumier">Droit coutumier</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Référence du titre (si dispo)</label>
                                <input name="reference_titre" type="text" placeholder="Ex: TF-12345/2025">
                            </div>
                            <div class="form-group">
                                <label>Année d'acquisition</label>
                                <input name="annee_acquisition" type="number" placeholder="Ex: 2020">
                            </div>
                            <div class="form-group full-width">
                                <label>Description détaillée *</label>
                                <textarea name="description" placeholder="Décrivez le terrain, ses atouts, accès, viabilisation, environnement..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3 : DOCUMENTS ET PHOTOS -->
                    <div class="form-section">
                        <h3><i class="fas fa-images" style="color: var(--or); margin-right: 12px;"></i> Photos</h3>
                        <div class="upload-area">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p style="font-weight: 600; margin-bottom: 8px;">Cliquez pour télécharger des photos</p>
                            <p style="color: var(--gris-moyen); font-size: 0.9rem;">jpg, jpeg, png, webp, svg, gif, tiff, raw, jfif jusqu'à 10 Mo</p>
                            <input type="file" name="photos[]" accept=".jpg,.jpeg,.png,.webp,.svg,.gif,.tiff,.raw,.jfif" multiple style="display:none;">
                        </div>
                            <div style="margin-top: 20px;">
                            <label style="display: block; margin-bottom: 12px; font-weight: 600;">Documents juridiques (optionnel)</label>
                            <div class="upload-area" style="padding: 20px;">
                                <i class="fas fa-file-pdf" style="font-size: 1.8rem;"></i>
                                <p style="font-weight: 600; margin-bottom: 4px;">Ajouter des documents</p>
                                <p style="color: var(--gris-moyen); font-size: 0.8rem;">Titre foncier, certificat, plans...</p>
                                    <input type="file" name="documents[]" accept=".pdf,.docx,.doc,.odt,.rtf,.txt,.xls,.xlsx,.ppt,.pptx,.ods,.odp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.oasis.opendocument.text,application/rtf,text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.presentation" style="display:none;">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4 : CONTACT ET VALIDATION -->
                    <div class="form-section">
                        <h3><i class="fas fa-shield" style="color: var(--or); margin-right: 12px;"></i> Vérification et mise en relation</h3>
                        <div style="background: rgba(199, 154, 62, 0.05); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                                <i class="fas fa-check-circle" style="color: var(--or); font-size: 1.3rem;"></i>
                                <span style="font-weight: 600;">Vérification d'identité requise</span>
                            </div>
                            <p style="color: var(--gris-moyen); margin-bottom: 16px;">Pour garantir la fiabilité des annonces, nous vérifions l'identité de chaque propriétaire.</p>
                            <div style="display: flex; gap: 12px;">
                                <span style="background: white; padding: 8px 16px; border-radius: 20px; border: 1px solid #e0e6ed;">Pièce d'identité</span>
                                <span style="background: white; padding: 8px 16px; border-radius: 20px; border: 1px solid #e0e6ed;">Justificatif de propriété</span>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <input type="checkbox" id="certify" name="certify" style="width: 18px; height: 18px;">
                            <label for="certify" style="font-weight: normal;">Je certifie que les informations fournies sont exactes et que je dispose des droits de publication sur ce terrain.</label>
                        </div>
                    </div>

                    <!-- BOUTONS DE SOUMISSION -->
                    <div style="display: flex; justify-content: flex-end; gap: 20px; margin-top: 40px;">
                        <button id="save-draft-btn" type="button" class="btn btn-outline">Enregistrer comme brouillon</button>
                        <button type="submit" class="btn btn-primary" style="background: var(--or); color: var(--bleu-pro);">Publier l'opportunité</button>
                    </div>
                </form>
            </div>
        </main>

        <footer>
            <div class="container">
                <div class="footer-grid">
                    <div>
                        <a href="index.php" class="footer-logo">KEL<span>FONCIA</span></a>
                        <p style="color: rgba(255,255,255,0.7); margin-bottom: 20px; max-width: 300px;">
                            Le logiciel de référence du foncier professionnel en RDC et Afrique.
                        </p>
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

        <script src="js/animations.js"></script>
        <script src="js/provinces-data.js"></script>
        <script src="js/api.js"></script>
        <script src="js/actions.js"></script>
        <script>
            // Initialize search section
            document.addEventListener('DOMContentLoaded', function() {
                const provinceSelectSearch = document.getElementById('province-select-search');
                const villeSelectSearch = document.getElementById('ville-select-search');
                const communeSelectSearch = document.getElementById('commune-select-search');
                const territoireSelectSearch = document.getElementById('territoire-select-search');
                const searchListingsContainer = document.getElementById('search-listings');
                const searchCountEl = document.getElementById('search-count');
                const applySearchBtn = document.getElementById('apply-search-btn');
                const resetSearchBtn = document.getElementById('reset-search-btn');
                const sortSearchSelect = document.getElementById('sort-search');

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

                // Initialise provinces
                setOptions(provinceSelectSearch, getProvinces(), 'Toutes les provinces');

                provinceSelectSearch.addEventListener('change', function () {
                    const prov = this.value;
                    if (!prov) {
                        setOptions(villeSelectSearch, [], 'Toutes les villes');
                        setOptions(communeSelectSearch, [], 'Toutes les communes');
                        setOptions(territoireSelectSearch, [], 'Tous les territoires');
                        villeSelectSearch.disabled = true;
                        communeSelectSearch.disabled = true;
                        territoireSelectSearch.disabled = true;
                        return;
                    }
                    const villes = getVillesByProvince(prov);
                    const territoires = getTerritoiresByProvince(prov);
                    setOptions(villeSelectSearch, villes, 'Toutes les villes');
                    setOptions(communeSelectSearch, [], 'Toutes les communes');
                    setOptions(territoireSelectSearch, territoires, 'Tous les territoires');
                    villeSelectSearch.disabled = villes.length === 0;
                    communeSelectSearch.disabled = true;
                    territoireSelectSearch.disabled = territoires.length === 0;
                });

                villeSelectSearch.addEventListener('change', function () {
                    const prov = provinceSelectSearch.value;
                    const ville = this.value;
                    if (!ville) {
                        setOptions(communeSelectSearch, [], 'Toutes les communes');
                        communeSelectSearch.disabled = true;
                        territoireSelectSearch.disabled = getTerritoiresByProvince(prov).length === 0;
                        return;
                    }
                    const communes = getCommunesByVille(prov, ville);
                    setOptions(communeSelectSearch, communes, 'Toutes les communes');
                    communeSelectSearch.disabled = communes.length === 0;
                    territoireSelectSearch.disabled = false;
                });

                communeSelectSearch.addEventListener('change', function () {
                    if (this.value) {
                        territoireSelectSearch.value = '';
                        territoireSelectSearch.disabled = true;
                    } else {
                        territoireSelectSearch.disabled = getTerritoiresByProvince(provinceSelectSearch.value).length === 0;
                    }
                });

                // Render listings function
                async function renderListings(rows) {
                    searchListingsContainer.innerHTML = '';
                    if (!rows || rows.length === 0) {
                        searchListingsContainer.innerHTML = `<div style="text-align: center; padding: 60px 20px; color: var(--gris-moyen); grid-column: 1 / -1;">
                            <i class="fas fa-search" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 20px;"></i>
                            <p>Aucune annonce ne correspond à votre recherche.</p>
                            <p style="font-size: 0.9rem;">Essayez un autre critère ou publiez votre terrain ci-dessous.</p>
                        </div>`;
                        if (searchCountEl) searchCountEl.textContent = '0 opportunité foncière';
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
                                <div style="display: flex; gap: 8px; margin-top: 16px;">
                                    <button class="fav-btn" data-fav-id="${l.id}" style="flex: 1; background: none; border: 1px solid #e0e6ed; cursor: pointer; padding: 8px 12px; border-radius: 8px; color: var(--or); font-weight: 600; transition: all 0.3s;">
                                        <i class="far fa-heart"></i> Favori
                                    </button>
                                    <button class="contact-btn" data-listing-id="${l.id}" style="flex: 1; background: var(--or); border: none; cursor: pointer; padding: 8px 12px; border-radius: 8px; color: white; font-weight: 600; transition: all 0.3s;">
                                        <i class="fas fa-envelope"></i> Contacter
                                    </button>
                                </div>
                            </div>`;
                        searchListingsContainer.appendChild(div);
                    });
                    
                    if (searchCountEl) searchCountEl.textContent = rows.length + ' opportunité' + (rows.length > 1 ? 's' : '') + ' foncière' + (rows.length > 1 ? 's' : '');
                    KelActions.attachFavoriteButtons('.fav-btn');
                    
                    // Attach contact buttons
                    searchListingsContainer.querySelectorAll('.contact-btn').forEach(btn => {
                        btn.addEventListener('click', async (e) => {
                            e.preventDefault();
                            if (!<?php echo $logged ? 'true' : 'false'; ?>) {
                                KelActions.showToast('Vous devez être connecté pour contacter le propriétaire', 'error');
                                setTimeout(() => window.location.href = 'connexion.php', 1000);
                                return;
                            }
                            
                            const listingId = btn.dataset.listingId;
                            btn.disabled = true;
                            btn.textContent = 'Chargement...';
                            
                            const listingRes = await KelActions.getListingDetails(listingId);
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-envelope"></i> Contacter';
                            
                            if (listingRes && listingRes.owner && listingRes.listing) {
                                const location = [listingRes.listing.ville, listingRes.listing.province].filter(x => x).join(', ');
                                const subject = `À propos de: ${listingRes.listing.title}${location ? ' - ' + location : ''}`;
                                window.contactMessageSubject = subject;
                                
                                KelActions.openContactModal(
                                    listingRes.owner.id,
                                    listingRes.owner.display_name || listingRes.owner.email,
                                    listingRes.owner.email,
                                    listingId,
                                    listingRes.listing.title,
                                    subject
                                );
                            } else {
                                KelActions.showToast('Erreur lors du chargement du propriétaire', 'error');
                            }
                        });
                    });
                }

                // Load listings with filters
                async function loadSearchListings(filters={}) {
                    searchListingsContainer.innerHTML = `<div style="text-align: center; padding: 60px 20px; grid-column: 1 / -1;">
                        <div style="display: inline-block;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: var(--or);"></i>
                            <p style="margin-top: 20px; color: var(--gris-moyen);">Chargement des annonces...</p>
                        </div>
                    </div>`;
                    
                    const res = await KelFonciaAPI.get('listings_list', filters);
                    if (!res || !res.ok) {
                        searchListingsContainer.innerHTML = `<div style="text-align: center; padding: 60px 20px; color: #dc2626; grid-column: 1 / -1;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; opacity: 0.5; display: block; margin-bottom: 20px;"></i>
                            <p>Erreur lors du chargement des annonces.</p>
                            <p style="font-size: 0.9rem; margin-top: 10px;">${res?.message || 'Veuillez réessayer.'}</p>
                        </div>`;
                        if (searchCountEl) searchCountEl.textContent = '0 opportunité foncière';
                        return;
                    }
                    renderListings(res.listings || []);
                }

                // Initial load
                loadSearchListings();

                // Apply filters button
                applySearchBtn && applySearchBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    const filters = {};
                    const prov = provinceSelectSearch.value;
                    const ville = villeSelectSearch.value;
                    const minArea = document.getElementById('min-area-search').value;
                    const maxArea = document.getElementById('max-area-search').value;
                    const budget = document.getElementById('budget-search').value;
                    const usage = document.getElementById('usage-search').value;
                    const statut = document.getElementById('statut-search').value;
                    
                    if (prov) filters.province = prov;
                    if (ville) filters.ville = ville;
                    if (minArea) filters.min_area = minArea;
                    if (maxArea) filters.max_area = maxArea;
                    if (budget) filters.max_price = budget;
                    if (usage) filters.usage = usage;
                    if (statut) filters.statut = statut;
                    
                    loadSearchListings(filters);
                });
                
                // Reset filters button
                resetSearchBtn && resetSearchBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    document.querySelectorAll('#province-select-search, #ville-select-search, #commune-select-search, #territoire-select-search').forEach(el => el.value = '');
                    document.querySelectorAll('#min-area-search, #max-area-search, #budget-search, #usage-search, #statut-search').forEach(el => el.value = '');
                    villeSelectSearch.disabled = true;
                    communeSelectSearch.disabled = true;
                    territoireSelectSearch.disabled = true;
                    loadSearchListings();
                });

                // Sort handler
                sortSearchSelect && sortSearchSelect.addEventListener('change', async function() {
                    // Get current listings
                    const listings = Array.from(searchListingsContainer.querySelectorAll('.terrain-card')).map(card => ({
                        title: card.querySelector('h3').textContent,
                        price: parseFloat(card.querySelector('.prix-valeur').textContent.replace(/[^0-9.-]/g, '')) || 0,
                        area: parseFloat(card.querySelector('.fa-ruler-combined')?.parentElement?.textContent.replace(/[^0-9.-]/g, '')) || 0
                    }));
                });
            });

            // Check if user is logged in
            document.addEventListener('DOMContentLoaded', function() {
                <?php if (!$logged): ?>
                    KelActions.showToast('Vous devez être connecté pour publier une annonce', 'error');
                    setTimeout(() => window.location.href = 'connexion.php', 1500);
                <?php endif; ?>

                const provinceSelect = document.getElementById('province-select');
                const villeSelect = document.getElementById('ville-select');
                const communeSelect = document.getElementById('commune-select');
                const territoireSelect = document.getElementById('territoire-select');

                // État initial : désactiver jusqu'à sélection
                villeSelect.disabled = true;
                communeSelect.disabled = true;
                territoireSelect.disabled = true;

                // Remplir les provinces au chargement
                function initProvinces() {
                    const provinces = getProvinces();
                    provinces.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province;
                        option.textContent = province;
                        provinceSelect.appendChild(option);
                    });
                }

                // Événement changement de province
                provinceSelect.addEventListener('change', function() {
                    const selectedProvince = this.value;
                    
                    // Réinitialiser les sélections
                    villeSelect.innerHTML = '<option value="">Sélectionnez une ville</option>';
                    communeSelect.innerHTML = '<option value="">Sélectionnez une commune</option>';
                    territoireSelect.innerHTML = '<option value="">Aucun territoire</option>';

                    if (!selectedProvince) {
                        villeSelect.disabled = true;
                        communeSelect.disabled = true;
                        territoireSelect.disabled = true;
                        return;
                    }

                    villeSelect.disabled = false;
                    
                    // Remplir les villes
                    const villes = getVillesByProvince(selectedProvince);
                    villes.forEach(ville => {
                        const option = document.createElement('option');
                        option.value = ville;
                        option.textContent = ville;
                        villeSelect.appendChild(option);
                    });

                    // Remplir les territoires
                    const territoires = getTerritoiresByProvince(selectedProvince);
                    if (territoires.length > 0) {
                        territoireSelect.disabled = false;
                        territoires.forEach(territoire => {
                            const option = document.createElement('option');
                            option.value = territoire;
                            option.textContent = territoire;
                            territoireSelect.appendChild(option);
                        });
                    } else {
                        territoireSelect.disabled = true;
                    }
                });

                // Événement changement de ville
                villeSelect.addEventListener('change', function() {
                    const selectedProvince = provinceSelect.value;
                    const selectedVille = this.value;
                    
                    // Réinitialiser les communes
                    communeSelect.innerHTML = '<option value="">Sélectionnez une commune</option>';

                    if (!selectedVille) {
                        communeSelect.disabled = true;
                        return;
                    }

                    communeSelect.disabled = false;
                    
                    // Remplir les communes
                    const communes = getCommunesByVille(selectedProvince, selectedVille);
                    communes.forEach(commune => {
                        const option = document.createElement('option');
                        option.value = commune;
                        option.textContent = commune;
                        communeSelect.appendChild(option);
                    });
                });

                // Événement changement de commune : désactive le territoire si une commune est choisie
                communeSelect.addEventListener('change', function() {
                    const selectedProvince = provinceSelect.value;
                    const selectedCommune = this.value;

                    if (selectedCommune) {
                        // Réinitialiser et désactiver le select territoire
                        territoireSelect.innerHTML = '<option value="">Aucun territoire</option>';
                        territoireSelect.disabled = true;
                    } else {
                        // Réactiver et remplir le territoire si la province en possède
                        territoireSelect.innerHTML = '<option value="">Aucun territoire</option>';
                        const territoires = getTerritoiresByProvince(selectedProvince);
                        if (territoires.length > 0) {
                            territoireSelect.disabled = false;
                            territoires.forEach(territoire => {
                                const option = document.createElement('option');
                                option.value = territoire;
                                option.textContent = territoire;
                                territoireSelect.appendChild(option);
                            });
                        } else {
                            territoireSelect.disabled = true;
                        }
                    }
                });

                // Initialisation
                initProvinces();

                // Wire upload areas
                document.querySelectorAll('.upload-area').forEach(area => {
                    const fileInput = area.querySelector('input[type=file]');
                    if (!fileInput) return;
                    area.addEventListener('click', () => fileInput.click());
                    fileInput.addEventListener('change', () => {
                        const cnt = fileInput.files.length;
                        const text = cnt > 1 ? cnt + ' fichiers sélectionnés' : (cnt === 1 ? '1 fichier sélectionné' : 'Ajouter des fichiers');
                        area.querySelector('p').textContent = text;
                        area.style.borderColor = cnt > 0 ? 'var(--or)' : '#e0e6ed';
                        // Allow multiple selections without disabling clicks
                        // User can keep adding files
                    });
                });

                // Draft handling
                const publishForm = document.querySelector('.publier-form');
                const publishInput = document.getElementById('is_published');
                const draftBtn = document.getElementById('save-draft-btn');
                if (draftBtn && publishForm && publishInput) {
                    draftBtn.addEventListener('click', () => {
                        publishInput.value = '0';
                        publishForm.requestSubmit();
                    });
                }

                // Attach create listing form
                KelActions.attachCreateListingForm('.publier-form');
            });
        </script>
    </body>
</html>