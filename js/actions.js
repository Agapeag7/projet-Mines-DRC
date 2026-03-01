// Handlers pour actions utilisateurs : login, create listing, toggle favorite
// Utilise les helpers KelFonciaAPI (js/api.js)
(function(){
    function showToast(msg, type = 'info'){
        // simple alert fallback; can be replaced by nicer UI
        if (type === 'error') console.error(msg);
        alert(msg);
    }

    async function login(email, password){
        if (!email || !password) return { error: 'missing_fields' };
        const res = await window.KelFonciaAPI.postJSON('login', { email, password });
        return res;
    }

    async function register(obj){
        // obj should contain email, password, role, display_name, phone, etc.
        if (!obj.email || !obj.password) return { error: 'missing_fields' };
        const res = await window.KelFonciaAPI.postJSON('register', obj);
        return res;
    }

    function attachLoginForm(selector){
        const form = document.querySelector(selector);
        if (!form) return;
        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const btn = form.querySelector('button[type=submit]');
            if (btn) btn.disabled = true;
            const formData = new FormData(form);
            const email = formData.get('email');
            const password = formData.get('password');
            const res = await login(email, password);
            if (btn) btn.disabled = false;
            if (res && res.ok) {
                showToast('Connecté: ' + (res.user.display_name || res.user.email));
                // optional: redirect or update UI
                window.location.reload();
            } else {
                showToast('Erreur connexion: ' + (res.error || JSON.stringify(res)), 'error');
            }
        });
    }

    function attachRegisterForm(selector){
        const form = document.querySelector(selector);
        if (!form) return;
        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const btn = form.querySelector('button[type=submit]');
            if (btn) btn.disabled = true;
            const formData = new FormData(form);
            const obj = {};
            formData.forEach((v,k)=>obj[k]=v);
            const res = await register(obj);
            if (btn) btn.disabled = false;
            if (res && res.ok) {
                showToast('Inscription réussie');
                // maybe auto login or redirect
                window.location.href = '/KelFoncia-DRC/connexion.php';
            } else {
                showToast('Erreur inscription: ' + (res.error || JSON.stringify(res)), 'error');
            }
        });
    }

    async function createListingFromForm(form){
        const res = await window.KelFonciaAPI.postForm('listings_create', form);
        return res;
    }

    function attachCreateListingForm(selector){
        const form = document.querySelector(selector);
        if (!form) return;
        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const btn = form.querySelector('button[type=submit]');
            if (btn) btn.disabled = true;
            const res = await createListingFromForm(form);
            if (btn) btn.disabled = false;
            if (res && res.ok) {
                showToast('Annonce créée (ID: '+res.id+')');
                // navigate to dashboard or update UI
                window.location.href = '/KelFoncia-DRC/tableau-de-bord.php';
            } else {
                showToast('Erreur création annonce: ' + (res.error || JSON.stringify(res)), 'error');
            }
        });
    }

    async function toggleFavorite(listingId){
        if (!listingId) return { error: 'missing_listing_id' };
        const res = await window.KelFonciaAPI.postJSON('toggle_favorite', { listing_id: listingId });
        return res;
    }

    function attachFavoriteButtons(selector){
        // selector matches elements with data-fav-id attribute or buttons
        document.addEventListener('click', async function(e){
            const el = e.target.closest(selector);
            if (!el) return;
            e.preventDefault();
            const id = el.dataset.favId || el.getAttribute('data-fav-id') || el.dataset.id;
            if (!id) { showToast('ID manquant', 'error'); return; }
            el.disabled = true;
            const res = await toggleFavorite(id);
            el.disabled = false;
            if (res && res.ok) {
                const action = res.result && res.result.action ? res.result.action : 'updated';
                showToast('Favori: ' + action);
                // toggle visual state
                el.classList.toggle('is-favorited', action === 'added');
            } else {
                showToast('Erreur favoris: ' + (res.error || JSON.stringify(res)), 'error');
            }
        });
    }

    // expose API
    window.KelActions = {
        login,
        attachLoginForm,
        createListingFromForm,
        attachCreateListingForm,
        toggleFavorite,
        attachFavoriteButtons,
        showToast
        ,register,
        attachRegisterForm
    };

})();
