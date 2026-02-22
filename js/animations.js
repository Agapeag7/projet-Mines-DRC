// ===== ANIMATIONS AVANCÉES KELFONCIA =====

document.addEventListener('DOMContentLoaded', function() {
    
    // 1️⃣ ANIMATIONS AU SCROLL (Intersection Observer)
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
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }

    // 4️⃣ CARTES INTERACTIVES (Leaflet)
    window.initMap = function(containerId, lat, lng, zoom = 12, markers = []) {
        if (!document.getElementById(containerId)) return;
        
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

    // 5️⃣ FILTRES DYNAMIQUES (simulation front)
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

    // 6️⃣ UPLOAD AREA SIMULATION
    const uploadAreas = document.querySelectorAll('.upload-area');
    uploadAreas.forEach(area => {
        area.addEventListener('click', function() {
            const input = document.createElement('input');
            input.type = 'file';
            input.multiple = true;
            input.accept = 'image/*';
            input.click();
            
            input.addEventListener('change', function(e) {
                const count = e.target.files.length;
                const icon = area.querySelector('i');
                const text = area.querySelector('p');
                if (count > 0) {
                    icon.className = 'fas fa-check-circle';
                    icon.style.color = '#27ae60';
                    if (text) text.innerHTML = count + ' fichier(s) sélectionné(s)';
                }
            });
        });
    });

    // 7️⃣ STICKY NAVBAR
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

    // 8️⃣ INIT MAPS
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

    // 9️⃣ ACTIVE NAVIGATION
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-links a').forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
    });
});

