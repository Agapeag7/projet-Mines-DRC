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
                        <h3><i class="fas fa-images" style="color: var(--or); margin-right: 12px;"></i> Photos (1-3 maximum)</h3>
                        <div class="upload-area-photos" style="border: 2px dashed #e0e6ed; padding: 30px; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.3s; margin-bottom: 20px;" id="photos-upload-area">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: var(--gris-moyen); margin-bottom: 12px; display: block;"></i>
                            <p style="font-weight: 600; margin-bottom: 8px;">Cliquez pour ajouter des photos (max 3)</p>
                            <p style="color: var(--gris-moyen); font-size: 0.9rem;">jpg, jpeg, png, webp jusqu'à 10 Mo chacune</p>
                            <p style="margin-top: 12px; font-size: 0.85rem; color: var(--or); font-weight: 600;" id="photos-count">0 photo ajoutée</p>
                            <input type="file" name="photos[]" id="photos-input" accept=".jpg,.jpeg,.png,.webp" style="display:none;">
                        </div>
                        <div id="photos-list" style="display: none; margin-bottom: 20px;">
                            <p style="font-weight: 600; margin-bottom: 12px;">Photos sélectionnées :</p>
                            <div id="photos-items" style="display: flex; flex-direction: column; gap: 8px;"></div>
                        </div>

                        <div style="margin-top: 30px;">
                            <h3 style="margin-bottom: 20px;"><i class="fas fa-file-pdf" style="color: var(--or); margin-right: 12px;"></i> Documents juridiques (optionnel, max 5)</h3>
                            <div class="upload-area-documents" style="border: 2px dashed #e0e6ed; padding: 30px; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.3s; margin-bottom: 20px;" id="documents-upload-area">
                                <i class="fas fa-file-pdf" style="font-size: 2.5rem; color: var(--gris-moyen); margin-bottom: 12px; display: block;"></i>
                                <p style="font-weight: 600; margin-bottom: 8px;">Cliquez pour ajouter des documents (max 5)</p>
                                <p style="color: var(--gris-moyen); font-size: 0.9rem;">Titre foncier, certificat, plans... PDF, Word, Excel, etc.</p>
                                <p style="margin-top: 12px; font-size: 0.85rem; color: var(--or); font-weight: 600;" id="documents-count">0 document ajouté</p>
                                <input type="file" name="documents[]" id="documents-input" accept=".pdf,.docx,.doc,.odt,.rtf,.txt,.xls,.xlsx,.ppt,.pptx,.ods,.odp" style="display:none;">
                            </div>
                            <div id="documents-list" style="display: none; margin-bottom: 20px;">
                                <p style="font-weight: 600; margin-bottom: 12px;">Documents sélectionnés :</p>
                                <div id="documents-items" style="display: flex; flex-direction: column; gap: 8px;"></div>
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

                // Handle photos upload
                const photosUploadArea = document.getElementById('photos-upload-area');
                const photosInput = document.getElementById('photos-input');
                const photosCountEl = document.getElementById('photos-count');
                const photosListEl = document.getElementById('photos-list');
                const photosItemsEl = document.getElementById('photos-items');
                const MAX_PHOTOS = 3;
                window.selectedPhotos = new DataTransfer();

                // Add hover effects
                photosUploadArea.addEventListener('mouseenter', () => {
                    if (window.selectedPhotos.items.length < MAX_PHOTOS) {
                        photosUploadArea.style.background = '#f5f7fa';
                        photosUploadArea.style.borderColor = 'var(--or)';
                    }
                });
                photosUploadArea.addEventListener('mouseleave', () => {
                    photosUploadArea.style.background = 'transparent';
                    photosUploadArea.style.borderColor = window.selectedPhotos.items.length > 0 ? 'var(--or)' : '#e0e6ed';
                });

                photosUploadArea.addEventListener('click', () => {
                    if (window.selectedPhotos.items.length < MAX_PHOTOS) {
                        photosInput.click();
                    } else {
                        KelActions.showToast('Vous avez atteint le maximum de 3 photos', 'error');
                    }
                });

                photosInput.addEventListener('change', (e) => {
                    const newFiles = Array.from(e.target.files);
                    const totalWillBe = window.selectedPhotos.items.length + newFiles.length;

                    if (totalWillBe > MAX_PHOTOS) {
                        const canAdd = MAX_PHOTOS - window.selectedPhotos.items.length;
                        KelActions.showToast(`Vous ne pouvez ajouter que ${canAdd} photo(s) de plus`, 'error');
                        photosInput.value = '';
                        return;
                    }

                    newFiles.forEach(file => {
                        window.selectedPhotos.items.add(file);
                    });

                    updatePhotosDisplay();
                    photosInput.value = '';
                });

                function updatePhotosDisplay() {
                    const count = window.selectedPhotos.items.length;
                    photosCountEl.textContent = count + ' photo' + (count > 1 ? 's' : '') + ' ajoutée' + (count > 1 ? 's' : '');
                    
                    if (count > 0) {
                        photosListEl.style.display = 'block';
                        photosItemsEl.innerHTML = '';
                        Array.from(window.selectedPhotos.items).forEach((item, index) => {
                            const file = item.getAsFile();
                            const div = document.createElement('div');
                            div.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f5f7fa; border-radius: 8px; border-left: 4px solid var(--or);';
                            div.innerHTML = `
                                <span style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-image" style="color: var(--or);"></i>
                                    <span style="font-size: 0.9rem;">${file.name}</span>
                                </span>
                                <button type="button" class="remove-photo-btn" data-index="${index}" style="background: none; border: none; color: #dc2626; cursor: pointer; padding: 4px 8px; font-weight: 600;">
                                    <i class="fas fa-trash-alt"></i> Supprimer
                                </button>
                            `;
                            photosItemsEl.appendChild(div);
                        });

                        document.querySelectorAll('.remove-photo-btn').forEach(btn => {
                            btn.addEventListener('click', (e) => {
                                e.preventDefault();
                                const index = parseInt(btn.dataset.index);
                                const newTransfer = new DataTransfer();
                                Array.from(window.selectedPhotos.items).forEach((item, i) => {
                                    if (i !== index) {
                                        newTransfer.items.add(item.getAsFile());
                                    }
                                });
                                window.selectedPhotos = newTransfer;
                                updatePhotosDisplay();
                            });
                        });
                    } else {
                        photosListEl.style.display = 'none';
                    }
                }

                // Handle documents upload
                const documentsUploadArea = document.getElementById('documents-upload-area');
                const documentsInput = document.getElementById('documents-input');
                const documentsCountEl = document.getElementById('documents-count');
                const documentsListEl = document.getElementById('documents-list');
                const documentsItemsEl = document.getElementById('documents-items');
                const MAX_DOCUMENTS = 5;
                window.selectedDocuments = new DataTransfer();

                // Add hover effects
                documentsUploadArea.addEventListener('mouseenter', () => {
                    if (window.selectedDocuments.items.length < MAX_DOCUMENTS) {
                        documentsUploadArea.style.background = '#f5f7fa';
                        documentsUploadArea.style.borderColor = 'var(--or)';
                    }
                });
                documentsUploadArea.addEventListener('mouseleave', () => {
                    documentsUploadArea.style.background = 'transparent';
                    documentsUploadArea.style.borderColor = window.selectedDocuments.items.length > 0 ? 'var(--or)' : '#e0e6ed';
                });

                documentsUploadArea.addEventListener('click', () => {
                    if (window.selectedDocuments.items.length < MAX_DOCUMENTS) {
                        documentsInput.click();
                    } else {
                        KelActions.showToast('Vous avez atteint le maximum de 5 documents', 'error');
                    }
                });

                documentsInput.addEventListener('change', (e) => {
                    const newFiles = Array.from(e.target.files);
                    const totalWillBe = window.selectedDocuments.items.length + newFiles.length;

                    if (totalWillBe > MAX_DOCUMENTS) {
                        const canAdd = MAX_DOCUMENTS - window.selectedDocuments.items.length;
                        KelActions.showToast(`Vous ne pouvez ajouter que ${canAdd} document(s) de plus`, 'error');
                        documentsInput.value = '';
                        return;
                    }

                    newFiles.forEach(file => {
                        window.selectedDocuments.items.add(file);
                    });

                    updateDocumentsDisplay();
                    documentsInput.value = '';
                });

                function updateDocumentsDisplay() {
                    const count = window.selectedDocuments.items.length;
                    documentsCountEl.textContent = count + ' document' + (count > 1 ? 's' : '') + ' ajouté' + (count > 1 ? 's' : '');
                    
                    if (count > 0) {
                        documentsListEl.style.display = 'block';
                        documentsItemsEl.innerHTML = '';
                        Array.from(window.selectedDocuments.items).forEach((item, index) => {
                            const file = item.getAsFile();
                            const div = document.createElement('div');
                            div.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f5f7fa; border-radius: 8px; border-left: 4px solid var(--or);';
                            div.innerHTML = `
                                <span style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-file" style="color: var(--or);"></i>
                                    <span style="font-size: 0.9rem;">${file.name}</span>
                                </span>
                                <button type="button" class="remove-document-btn" data-index="${index}" style="background: none; border: none; color: #dc2626; cursor: pointer; padding: 4px 8px; font-weight: 600;">
                                    <i class="fas fa-trash-alt"></i> Supprimer
                                </button>
                            `;
                            documentsItemsEl.appendChild(div);
                        });

                        document.querySelectorAll('.remove-document-btn').forEach(btn => {
                            btn.addEventListener('click', (e) => {
                                e.preventDefault();
                                const index = parseInt(btn.dataset.index);
                                const newTransfer = new DataTransfer();
                                Array.from(window.selectedDocuments.items).forEach((item, i) => {
                                    if (i !== index) {
                                        newTransfer.items.add(item.getAsFile());
                                    }
                                });
                                window.selectedDocuments = newTransfer;
                                updateDocumentsDisplay();
                            });
                        });
                    } else {
                        documentsListEl.style.display = 'none';
                    }
                }

                // Draft handling & file submission
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