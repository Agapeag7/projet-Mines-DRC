// ===== ANIMATIONS AVANCÉES KELFONCIA =====
console.log('animations.js loaded');

function main() {
    console.log('main() start');
    
    // 0️⃣ ROTATION DES PROVINCES (Hero Title)
    const provincesList = [
        "Kinshasa", "Kasai", "Kasai Central", "Kasai Oriental", "Lualaba", "Haut Katanga",
        "Haut Lomami", "Katanga", "Maniema", "Nord Kivu", "Sud Kivu", "Ituri", "Tshopo",
        "Bas Uele", "Haut Uele", "Équateur", "Kasai", "Kinshasa", "Kongo Central"
    ];
    
    let provinceIndex = 0;
    const rotatingProvince = document.getElementById('rotating-province');
    
    if (rotatingProvince) {
        // Ajouter le style de transition
        rotatingProvince.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        function rotateProvince() {
            // Animation de sortie
            rotatingProvince.style.opacity = '0';
            rotatingProvince.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                // Changer le texte
                provinceIndex = (provinceIndex + 1) % provincesList.length;
                rotatingProvince.textContent = provincesList[provinceIndex];
                
                // Animation d'entrée
                rotatingProvince.style.opacity = '1';
                rotatingProvince.style.transform = 'translateY(0)';
            }, 250);
        }
        
        // Changer de province toutes les 3 secondes
        setInterval(rotateProvince, 3000);
    }
    
    
    const observerOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                
                // Animation spéciale pour les statistiques
                if (entry.target.classList.contains('stat-nombre')) {
                    animateNumber(entry.target);
                }
            }
        });
    }, observerOptions);

    // Observer tous les éléments avec classes d'animation
    document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right, .stat-nombre').forEach(el => {
        observer.observe(el);
    });

    // 2️⃣ ANIMATION DES CHIFFRES
    function animateNumber(element) {
        const value = element.innerText;
        if (value.includes('+')) {
            const number = parseInt(value.replace(/[^0-9]/g, ''));
            let current = 0;
            const increment = number / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= number) {
                    element.innerText = '+' + number;
                    clearInterval(timer);
                } else {
                    element.innerText = '+' + Math.floor(current);
                }
            }, 20);
        }
    }

    // 3️⃣ MOBILE MENU TOGGLE
    const mobileMenuBtn = document.querySelector('.mobile-menu');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuBtn && navLinks) {
        const icon = mobileMenuBtn.querySelector('i');

        mobileMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const opened = navLinks.classList.toggle('active');
            // swap icon
            if (icon) {
                icon.classList.toggle('fa-bars', !opened);
                icon.classList.toggle('fa-times', opened);
            }
        });

        // Close when clicking a link
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });

        // Close when clicking outside
        document.addEventListener('click', () => {
            if (navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }

    // 5️⃣ CARTES INTERACTIVES (Leaflet)
    window.initMap = function(containerId, lat, lng, zoom = 12, markers = []) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        // Remove existing map if any
        if (container._leaflet_map) {
            container._leaflet_map.remove();
        }
        
        const map = L.map(containerId).setView([lat, lng], zoom);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap, CartoDB',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);
        
        markers.forEach(m => {
            L.marker([m.lat, m.lng]).addTo(map)
                .bindPopup(m.title);
        });
        
        map.scrollWheelZoom.disable();
        return map;
    };

    // 6️⃣ FILTRES DYNAMIQUES (simulation front)
    const filtresForm = document.querySelector('.filtres-avances');
    if (filtresForm) {
        const inputs = filtresForm.querySelectorAll('select, input');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                // Simule un chargement
                const terrainCards = document.querySelectorAll('.terrain-card');
                terrainCards.forEach(card => {
                    card.style.opacity = '0.5';
                    setTimeout(() => {
                        card.style.opacity = '1';
                    }, 300);
                });
            });
        });
    }

    // 7️⃣ UPLOAD AREA SIMULATION
    const uploadAreas = document.querySelectorAll('.upload-area');
    uploadAreas.forEach(area => {
        area.addEventListener('click', function() {
            const input = document.createElement('input');
            input.type = 'file';
            input.multiple = true;
            input.accept = 'image/*';
            input.click();
            
            input.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                const count = files.length;
                const icon = area.querySelector('i');
                const text = area.querySelector('p');
                if (count > 0) {
                    icon.className = 'fas fa-check-circle';
                    icon.style.color = '#27ae60';
                    if (text) text.innerHTML = count + ' fichier(s) sélectionné(s)';
                }
                // preview
                let preview = area.querySelector('.upload-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.className = 'upload-preview';
                    area.appendChild(preview);
                }
                preview.innerHTML = '';
                files.forEach(f => {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        const img = document.createElement('img');
                        img.src = evt.target.result;
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(f);
                });
            });
        });
    });

    // 8️⃣ STICKY NAVBAR
    const header = document.querySelector('header');
    if (header) {
        let lastScroll = 0;
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            if (currentScroll > lastScroll && currentScroll > 100) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }
            lastScroll = currentScroll;
        });
    }

    // 9️⃣ INIT MAPS
    // Carte accueil
    if (document.getElementById('map')) {
        initMap('map', -4.325, 15.322, 12, [
            { lat: -4.330, lng: 15.315, title: 'Gombe - 4500m²' },
            { lat: -4.350, lng: 15.295, title: 'Lingwala - 1200m²' },
            { lat: -4.380, lng: 15.340, title: 'Limete - 8000m²' }
        ]);
    }
    
    // Carte recherche
    if (document.getElementById('map-recherche')) {
        initMap('map-recherche', -4.325, 15.322, 12, [
            { lat: -4.330, lng: 15.315, title: 'Gombe - 4500m²' },
            { lat: -4.345, lng: 15.280, title: 'Ngaliema - 2500m²' },
            { lat: -4.350, lng: 15.295, title: 'Lingwala - 1200m²' },
            { lat: -4.380, lng: 15.340, title: 'Limete - 8000m²' },
            { lat: -4.400, lng: 15.300, title: 'Mont Ngafula - 10000m²' }
        ]);
    }

    // 🔟 FIL D'ACTUALITÉS (données simulées)
    function initFeed() {
        console.log('initFeed called');
        const container = document.getElementById('feed-list');
        if (!container) {
            console.warn('feed-list container not found');
            return;
        }
        // montre palette modal
        const modal = document.getElementById('detail-modal');
        const modalBody = document.getElementById('modal-body');
        const closeBtn = modal && modal.querySelector('.modal-close');
        if (closeBtn) closeBtn.addEventListener('click', () => { modal.style.display = 'none'; });
        // fermer en cliquant sur la zone grisée en dehors du contenu
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.style.display = 'none';
            });
        }
        function showTerrainDetail(item) {
            if (!modal || !modalBody) return;
            item.comments = item.comments || [];
            modalBody.innerHTML = `
                <h2 class="modal-title">${item.title}</h2>
                <div class="modal-body-grid">
                    <div class="modal-left-col">
                        <div class="terrain-image-large">
                            ${item.img ? `<img src="${item.img}" alt="${item.title}"/>` : '<i class="fas fa-map placeholder-icon"></i>'}
                        </div>
                        <div class="comments-section">
                            <h3>Commentaires (${item.comments.length})</h3>
                            <ul class="comment-list">
                                ${item.comments.map(c => `<li>${c}</li>`).join('')}
                            </ul>
                            <div class="comment-form">
                                <textarea id="modal-comment-input" placeholder="Écrire un commentaire..."></textarea>
                                <button id="modal-comment-submit" class="btn btn-primary btn-comment">Envoyer</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-right-col">
                        <div class="terrain-info">
                            <p><strong>Date :</strong> ${item.date}</p>
                            <p><strong>Superficie :</strong> ${item.size}</p>
                            <p><strong>Statut juridique :</strong> ${item.statut}</p>
                            <p><strong>Localisation :</strong> ${item.location}</p>
                            ${item.address ? `<p><strong>Adresse :</strong> ${item.address}</p>` : ``}
                            ${item.usage ? `<p><strong>Usage :</strong> ${item.usage}</p>` : ``}
                            ${item.reference ? `<p><strong>Réf. titre :</strong> ${item.reference}</p>` : ``}
                            ${item.annee ? `<p><strong>Année :</strong> ${item.annee}</p>` : ``}
                            <p><strong>Prix :</strong> ${item.price}</p>
                            ${item.description ? `<p class="terrain-desc"><strong>Description :</strong><br>${item.description}</p>` : ``}
                            <p>${item.verified ? '<span class="terrain-statut statut-verifie"><i class="fas fa-check-circle"></i> Vérifié</span>' : ''} ${item.available ? '<span class="terrain-statut statut-disponible">Disponible</span>' : '<span class="terrain-statut">Indisponible</span>'}</p>
                        </div>
                        <div class="payment-box">
                            <p>Pour poster un commentaire, vous devez <strong>payer</strong> 2 $.</p>
                            <button class="btn btn-or btn-payment" id="modal-payment-btn">Payer</button>
                        </div>
                    </div>
                </div>
                <p class="more-info">Plus d'infos ici...</p>
            `;
            const submit = document.getElementById('modal-comment-submit');
            const input = document.getElementById('modal-comment-input');
            const payBtn = document.getElementById('modal-payment-btn');
            if (submit && input) {
                submit.addEventListener('click', () => {
                    const text = input.value.trim();
                    if (text) {
                        item.comments.push(text);
                        const list = modalBody.querySelector('.comment-list');
                        if (list) {
                            const li = document.createElement('li');
                            li.textContent = text;
                            list.appendChild(li);
                            list.parentElement.querySelector('h3').textContent = `Commentaires (${item.comments.length})`;
                        }
                        input.value = '';
                        const card = document.querySelector(`.terrain-card[data-title="${item.title}"]`);
                        if (card) {
                            const em = card.querySelector('.comment-btn em');
                            if (em) em.innerText = item.comments.length;
                        }
                    }
                });
            }
            if (payBtn) {
                payBtn.addEventListener('click', () => {
                    alert('Paiement simulé : implémentation future.');
                });
            }
            modal.style.display = 'flex';
        }
        // Données simulées (dans une app réelle, récupérer via API)
        const allItems = [
            {
                title: "Terrain résidentiel - Gombe",
                date: "2026-02-25",
                size: "4 500 m²",
                statut: "Titre foncier",
                location: "Kinshasa, Gombe",
                province: "Kinshasa",
                price: "$450 000",
                verified: true,
                available: true,
                address: "Avenue du Commerce, n°45",
                usage: "Résidentiel",
                statut_juridique: "Titre foncier",
                reference: "TF-12345/2025",
                annee: 2020,
                description: "Magnifique terrain dans le quartier huppé de Gombe, proche des services administratifs.",
                img: "https://via.placeholder.com/180x140",
                comments: []
            },

            {
                title: "Terrain commercial - Limete",
                date: "2026-02-23",
                size: "8 200 m²",
                statut: "Certificat",
                location: "Kinshasa, Limete",
                province: "Kinshasa",
                price: "$720 000",
                verified: true,
                available: true,
                address: "Quartier Limete, avenue de la Paix",
                usage: "Commercial",
                description: "Terrain bien situé à Limete, idéal pour commerce ou bureaux.",
                img: "https://via.placeholder.com/180x140",
                comments: []
            },
            {
                title: "Terrain industriel - Mont Ngafula",
                date: "2026-02-20",
                size: "12 500 m²",
                statut: "Titre foncier",
                location: "Kinshasa, Mont Ngafula",
                province: "Kinshasa",
                price: "$950 000",
                verified: true,
                available: true,
                address: "Route de Matadi, Mont Ngafula",
                usage: "Industriel",
                description: "Grand terrain plat, parfait pour usine ou entrepôt.",
                img: "https://via.placeholder.com/180x140",
                comments: []
            },
            {
                title: "Terrain agricole - Matadi",
                date: "2026-02-18",
                size: "25 000 m²",
                statut: "Contrat de location",
                location: "Bas-Congo, Matadi",
                province: "Kongo Central",
                price: "$1 200 000",
                verified: false,
                available: true,
                usage: "Agricole",
                description: "Terres fertiles, proche du port maritime.",
                img: "https://via.placeholder.com/180x140",
                comments: []
            },
            {
                title: "Lot urbain - Lubumbashi",
                date: "2026-02-15",
                size: "1 200 m²",
                statut: "Certificat",
                location: "Haut Katanga, Lubumbashi",
                province: "Haut-Katanga",
                price: "$300 000",
                verified: true,
                available: false,
                address: "Avenue Lubumashi 3ème",
                usage: "Résidentiel",
                img: "https://via.placeholder.com/180x140",
                comments: []
            },
            {
                title: "Terrain mixte - Kisangani",
                date: "2026-02-10",
                size: "3 500 m²",
                statut: "Titre foncier",
                location: "Tshopo, Kisangani",
                province: "Tshopo",
                price: "$500 000",
                verified: true,
                available: true,
                address: "Quartier Makiso, Kisangani",
                usage: "Mixte",
                description: "Proche du fleuve, mixte résidentiel/commercial.",
                img: "https://via.placeholder.com/180x140",
                comments: []
            }
        ];

        let currentItems = [...allItems];
        let displayedCount = 0;
        const perPage = 3;

        const provinceSelect = document.getElementById('filter-province');
        const statutSelect = document.getElementById('filter-statut');
        const keywordInput = document.getElementById('filter-keyword');
        const applyBtn = document.getElementById('filter-apply');
        const resetBtn = document.getElementById('filter-reset');
        const loadBtn = document.getElementById('load-more');

        function renderItems(reset = false) {
            if (reset) {
                container.innerHTML = '';
                displayedCount = 0;
            }
            const slice = currentItems.slice(displayedCount, displayedCount + perPage);
            slice.forEach((item, idx) => {
                const card = document.createElement('div');
                card.className = 'terrain-card fade-in';
                const bgColor = ['#e3f2fd', '#f3e5f5', '#e8f5e9', '#fff3e0', '#fce4ec'][idx % 5];
                card.innerHTML = `
                    <div class="terrain-image" style="background-color: ${bgColor}; display: flex; align-items: center; justify-content: center; font-size: 2rem; border-radius: 8px;"><i class="fas fa-map"></i></div>
                    <div class="terrain-infos">
                        <div class="feed-date">${item.date}</div>
                        <h3>${item.title}</h3>
                        <div class="terrain-details">
                            <span class="terrain-detail-item"><i class="fas fa-ruler-combined"></i> ${item.size}</span>
                            <span class="terrain-detail-item"><i class="fas fa-file-signature"></i> ${item.statut}</span>
                            <span class="terrain-detail-item"><i class="fas fa-map-pin"></i> ${item.location}</span>
                        </div>
                        ${item.verified ? '<span class="terrain-statut statut-verifie"><i class="fas fa-check-circle"></i> Vérifié</span>' : ''}
                        <div class="feed-actions">
                            <span class="like-btn"><i class="fas fa-heart"></i> <em>0</em></span>
                            <span class="comment-btn"><i class="fas fa-comment"></i> <em>${item.comments ? item.comments.length : 0}</em></span>
                        </div>
                    </div>
                    <div class="terrain-prix">
                        <span class="prix-valeur">${item.price}</span>
                        <span class="prix-devise">USD</span>
                        ${item.available ? '<span class="terrain-statut statut-disponible" style="margin-top: 16px;">Disponible</span>' : ''}
                    </div>
                `;
                attachInteractions(card, item);
                card.addEventListener('click', () => showTerrainDetail(item));
                // mark card so updates can find it
                card.setAttribute('data-title', item.title);
                container.appendChild(card);
                // make visible immediately and observe if observer exists
                if (observer) {
                    observer.observe(card);
                }
                card.classList.add('visible');
            });
            displayedCount += slice.length;
            if (loadBtn) {
                if (displayedCount >= currentItems.length) {
                    loadBtn.style.display = 'none';
                } else {
                    loadBtn.style.display = 'inline-block';
                }
            }
        }

        function attachInteractions(card, item) {
            const likeSpan = card.querySelector('.like-btn');
            const commentSpan = card.querySelector('.comment-btn');
            likeSpan.addEventListener('click', (e) => {
                e.stopPropagation();
                likeSpan.classList.toggle('liked');
                const countEl = likeSpan.querySelector('em');
                let cnt = parseInt(countEl.innerText, 10);
                cnt += likeSpan.classList.contains('liked') ? 1 : -1;
                countEl.innerText = cnt;
            });
            commentSpan.addEventListener('click', (e) => {
                e.stopPropagation();
                showTerrainDetail(item);
                setTimeout(() => {
                    const textarea = document.getElementById('modal-comment-input');
                    if (textarea) textarea.focus();
                }, 50);
            });
        }

        function applyFilters() {
            if (!provinceSelect || !statutSelect || !keywordInput) return;
            const prov = provinceSelect.value;
            const stat = statutSelect.value;
            const key = keywordInput.value.toLowerCase();
            currentItems = allItems.filter(it => {
                let ok = true;
                if (prov && it.province !== prov) ok = false;
                if (stat && it.statut !== stat) ok = false;
                if (key && !it.title.toLowerCase().includes(key)) ok = false;
                return ok;
            });
            renderItems(true);
        }

        function resetFilters() {
            if (provinceSelect) provinceSelect.value = '';
            if (statutSelect) statutSelect.value = '';
            if (keywordInput) keywordInput.value = '';
            currentItems = [...allItems];
            renderItems(true);
        }

        // populate province list (simply unique values)
        if (provinceSelect) {
            const provs = [...new Set(allItems.map(i => i.province))];
            provs.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p; opt.innerText = p;
                provinceSelect.appendChild(opt);
            });
            applyBtn.addEventListener('click', applyFilters);
            resetBtn.addEventListener('click', resetFilters);
        }

        if (loadBtn) loadBtn.addEventListener('click', () => renderItems());

        // initial render
        resetFilters();
    }

    // only run feed initializer on pages that actually have the container
    if (document.getElementById('feed-list')) {
        initFeed();
    }

    // 🔟 ACTIVE NAVIGATION
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-links a').forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
    });
}

// ensure main runs even if DOMContentLoaded already fired
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', main);
} else {
    main();
}

