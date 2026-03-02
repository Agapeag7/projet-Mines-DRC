// Handlers pour actions utilisateurs : login, create listing, toggle favorite
// Utilise les helpers KelFonciaAPI (js/api.js)
(function(){
    function showToast(msg, type = 'info'){
        // Create or get toast container
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                font-family: 'Inter', sans-serif;
            `;
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        const bgColor = type === 'error' ? '#dc2626' : type === 'success' ? '#10b981' : '#0a3143';
        const icon = type === 'error' ? '✕' : type === 'success' ? '✓' : 'ℹ';
        
        toast.style.cssText = `
            background: ${bgColor};
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
            max-width: 400px;
            word-wrap: break-word;
        `;
        toast.innerHTML = `<strong>${icon}</strong> ${msg}`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Add animation styles
    if (!document.getElementById('toast-styles')) {
        const style = document.createElement('style');
        style.id = 'toast-styles';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }

    async function login(email, password){
        if (!email || !password) {
            showToast('Email et mot de passe requis', 'error');
            return { error: 'missing_fields' };
        }
        const res = await window.KelFonciaAPI.postJSON('login', { email, password });
        // if backend indicates 2FA is needed, prompt user for code
        if (res && res.need_2fa) {
            const code = prompt('Entrez le code de vérification envoyé');
            if (code) {
                return await verify2fa(code);
            } else {
                return { error: '2fa_cancelled' };
            }
        }
        return res;
    }

    async function register(obj){
        console.debug('register payload', obj);
        // basic required
        if (!obj.email || !obj.password || !obj.display_name) {
            showToast('Email, mot de passe et nom sont obligatoires', 'error');
            return { error: 'missing_fields' };
        }
        // RFC‑like email validation
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2}$/;
        if (!emailRegex.test(obj.email)) {
            showToast('Adresse e‑mail invalide', 'error');
            return { error: 'invalid_email' };
        }
        if (obj.password !== obj.password_confirm) {
            showToast('Les mots de passe ne correspondent pas', 'error');
            return { error: 'password_mismatch' };
        }
        if (obj.password.length < 8) {
            showToast('Le mot de passe doit contenir au moins 8 caractères', 'error');
            return { error: 'password_weak' };
        }
        // phone optional but if provided must match RDC patterns
        if (obj.phone) {
            const phoneRegex = /^(?:(?:099|097|081|082|086)\d{7}|(?:\+243|243|0)(?:99|97|81|82|86)\d{7})$/;
            if (!phoneRegex.test(obj.phone)) {
                showToast('Numéro de téléphone invalide', 'error');
                return { error: 'invalid_phone' };
            }
        }
        const res = await window.KelFonciaAPI.postJSON('register', obj);
        // after registration attempt, if we have accumulated KYC evidence send it
        if (res && res.ok && window._kycEvidence && window._kycEvidence.length) {
            const payload = { type: window._kycType || 'id_card', evidence: window._kycEvidence };
            try {
                await requestKyc(payload.type, payload.evidence);
                // optionally notify user
                showToast('Vérification KYC envoyée', 'success');
            } catch(e){
                console.error('KYC request failed', e);
            }
        }
        return res;
    }

    function attachLoginForm(selector){
        const form = document.querySelector(selector);
        if (!form) return;
        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const btn = form.querySelector('button[type=submit]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Connexion en cours...';
            }
            const formData = new FormData(form);
            const email = formData.get('email');
            const password = formData.get('password');
            const res = await login(email, password);
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Se connecter';
            }
            if (res && res.ok) {
                showToast('Connexion réussie!', 'success');
                setTimeout(() => {
                    window.location.href = 'tableau-de-bord.php';
                }, 1000);
            } else {
                const errMsg = res?.message || res?.error || 'Erreur de connexion';
                showToast(errMsg, 'error');
            }
        });
    }

    function attachRegisterForm(selector){
        const form = document.querySelector(selector);
        if (!form) return;
        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const btn = form.querySelector('button[type=submit]');
            const termsCheckbox = form.querySelector('#accept-terms');
            
            if (!termsCheckbox || !termsCheckbox.checked) {
                showToast('Vous devez accepter les conditions générales', 'error');
                return;
            }

            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Création en cours...';
            }

            const formData = new FormData(form);
            const obj = {};
            formData.forEach((v,k) => {
                if (k !== 'accept-terms') obj[k] = v;
            });
            
            const res = await register(obj);
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Créer mon compte professionnel';
            }
            if (res && res.ok) {
                showToast('Inscription réussie! Redirection...', 'success');
                setTimeout(() => {
                    window.location.href = 'tableau-de-bord.php';
                }, 1500);
            } else {
                const errMsg = res?.message || res?.error || 'Erreur d\'inscription';
                showToast(errMsg, 'error');
            }
        });
    }

    async function verify2fa(code){
        if (!code) return { error: 'missing_code' };
        return await window.KelFonciaAPI.postJSON('verify_2fa', { code });
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
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Création en cours...';
            }
            const res = await createListingFromForm(form);
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Publier mon annonce';
            }
            if (res && res.ok) {
                showToast('Annonce créée avec succès!', 'success');
                setTimeout(() => {
                    window.location.href = 'tableau-de-bord.php';
                }, 1000);
            } else {
                const errMsg = res?.message || res?.error || 'Erreur de création';
                showToast(errMsg, 'error');
            }
        });
    }

    async function toggleFavorite(listingId){
        if (!listingId) {
            showToast('Problème: ID manquant', 'error');
            return { error: 'missing_listing_id' };
        }
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
            if (!id) { 
                showToast('ID manquant', 'error');
                return;
            }
            el.disabled = true;
            const res = await toggleFavorite(id);
            el.disabled = false;
            if (res && res.ok) {
                const action = res.result && res.result.action ? res.result.action : 'updated';
                const msg = action === 'added' ? '❤ Ajouté aux favoris' : '♡ Retiré des favoris';
                showToast(msg, 'success');
                // toggle visual state
                el.classList.toggle('is-favorited', action === 'added');
            } else {
                const errMsg = res?.message || res?.error || 'Erreur';
                showToast(errMsg, 'error');
            }
        });
    }

    // KYC helpers
    async function requestKyc(type, evidence){
        if (!type || !evidence || !evidence.length) return { error:'missing' };
        return await window.KelFonciaAPI.postJSON('kyc_request', { type, evidence });
    }

    // expose API
    window.KelActions = {
        login,
        verify2fa,
        attachLoginForm,
        createListingFromForm,
        attachCreateListingForm,
        toggleFavorite,
        attachFavoriteButtons,
        showToast,
        register,
        attachRegisterForm,
        requestKyc
    };

})();
