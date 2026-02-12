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
window.onload = function() {
    initMap();
    handleFadeIn();
    window.addEventListener('scroll', handleFadeIn);
    window.addEventListener('resize', handleFadeIn);
};