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