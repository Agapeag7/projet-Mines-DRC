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
            const msg = 'Email et mot de passe requis';
            showToast(msg, 'error');
            return { error: 'missing_fields', message: msg };
        }
        const payload = { email, password };
        // when debugging locally it can be handy to receive the 2FA code
        if (location.search.includes('debug')) {
            payload.debug = 1;
        }
        const res = await window.KelFonciaAPI.postJSON('login', payload);
        // if backend indicates 2FA is needed, handle code entry or debug flow
        if (res && res.need_2fa) {
            if (res.debug_code) {
                // development mode: server returned the code directly
                console.debug('2FA debug code received from server', res.debug_code);
                showToast('Code de vérification (debug) : ' + res.debug_code, 'info');
                // skip prompt and automatically verify
                return await verify2fa(res.debug_code);
            }

            showToast('Un code a été envoyé à votre adresse email. Vérifiez votre boîte (ou ajoutez ?debug à l\'URL pour voir le code).', 'info');
            const code = prompt('Entrez le code de vérification reçu par email');
            if (code) {
                return await verify2fa(code);
            } else {
                return { error: '2fa_cancelled' };
            }
        }
        return res;
    }

    async function register(obj){
        // if the page somehow allows a logged-in user to submit the form we
        // should bail out early and avoid sending a request that will likely
        // redirect or trigger backend warnings (which lead to non_json_response).
        if (window.__loggedIn) {
            showToast('Vous êtes déjà connecté. Déconnectez-vous pour créer un compte.', 'error');
            return { error: 'already_logged_in' };
        }

        console.debug('register payload', obj);
        // clear preview at beginning of registration attempt (user may have re‑submitted)
        const prevEl = document.getElementById('face-preview-container');
        if (prevEl) prevEl.innerHTML = '';
        // basic required
        if (!obj.email || !obj.password || !obj.display_name || !obj.password_confirm) {
            const msg = 'Email, mot de passe, confirmation et nom sont obligatoires';
            showToast(msg, 'error');
            return { error: 'missing_fields', message: msg };
        }
        // RFC‑like email validation
        // const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2}$/;
        const emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
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
            // allow 0 or +243/243 prefix and then 9 digits starting with 8 or 9
            const phoneRegex = /^(?:\+243|243|0)(?:[89]\d{8})$/;
            if (!phoneRegex.test(obj.phone)) {
                showToast('Numéro de téléphone invalide', 'error');
                return { error: 'invalid_phone' };
            }
        }
        // attach descriptor if available; server will perform the duplicate check
        if (window._kycDescriptor) {
            obj.face_descriptor = window._kycDescriptor;
        }
        // send the captured photo (base64) if available
        if (window._kycFacePhoto) {
            obj.face_photo = window._kycFacePhoto;
        }
        const res = await window.KelFonciaAPI.postJSON('register', obj);
        // clear any cached face/kyc data whether registration succeeded or not;
        // if the call failed the user can retake a new photo later
        window._kycDescriptor = null;
        window._kycFacePhoto = null;
        window._faceExists = false;
        if (window._kycEvidence) {
            window._kycEvidence = [];
            window._kycType = null;
        }
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
            // trim string values to avoid sending only spaces
            Object.keys(obj).forEach(k => {
                if (typeof obj[k] === 'string') obj[k] = obj[k].trim();
            });
            console.debug('register payload', obj);
            
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
                console.debug('register response', res);
                let errMsg = res?.message || res?.error || 'Erreur d\'inscription';
                // server sometimes returns a list of missing fields
                if (res?.error === 'missing_fields') {
                    if (res.missing && res.missing.length) {
                        errMsg = res.message || 'Champs manquants : ' + res.missing.join(', ');
                    } else if (res.message) {
                        errMsg = res.message;
                    }
                }
                if (res?.error === 'non_json_response') {
                    errMsg = 'Réponse serveur inattendue (voir console)';
                    console.debug('raw server response', res.raw);
                }
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
            const isDraft = form.querySelector('#is_published') && form.querySelector('#is_published').value === '0';
            const certify = form.querySelector('#certify');
            if (!isDraft && (!certify || !certify.checked)) {
                KelActions.showToast('Vous devez certifier que les informations sont exactes.', 'error');
                return;
            }
            const btn = form.querySelector('button[type=submit]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Création en cours...';
            }
            const invalidDocument = Array.from(form.querySelectorAll('input[name="documents[]"]'))
                .flatMap(input => Array.from(input.files || []))
                .find(file => !/[.](pdf|doc|docx|odt|rtf|txt|xls|xlsx|ppt|pptx|ods|odp)$/i.test(file.name));
            if (invalidDocument) {
                KelActions.showToast('Document détecté non autorisé : ' + invalidDocument.name, 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Publier mon annonce';
                }
                return;
            }

            const oversizedFile = Array.from(form.querySelectorAll('input[type="file"]'))
                .flatMap(input => Array.from(input.files || []))
                .find(file => file.size > 10 * 1024 * 1024);
            if (oversizedFile) {
                KelActions.showToast('Fichier trop volumineux : ' + oversizedFile.name, 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Publier mon annonce';
                }
                return;
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
                if (res?.details && Array.isArray(res.details)) {
                    errMsg += ' : ' + res.details.join(', ');
                }
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

    // Messaging helpers
    async function initiateConversation(ownerId, listingId, ownerName, listingTitle) {
        if (!ownerId) {
            showToast('Erreur: propriétaire non trouvé', 'error');
            return { error: 'missing_owner' };
        }
        
        // Create a conversation
        const sujet = listingTitle ? `À propos de: ${listingTitle}` : 'Nouvelle conversation';
        const res = await window.KelFonciaAPI.postJSON('conversation_create', {
            sujet: sujet,
            listing_id: listingId,
            participants: [ownerId]
        });
        
        if (res && res.ok) {
            return res;
        } else {
            const errMsg = res?.message || res?.error || 'Erreur lors de la création de la conversation';
            showToast(errMsg, 'error');
            return res;
        }
    }

    async function sendMessage(conversationId, content) {
        if (!conversationId || !content) {
            showToast('Conversation ou message manquant', 'error');
            return { error: 'missing_fields' };
        }
        
        const res = await window.KelFonciaAPI.postJSON('message_send', {
            conversation_id: conversationId,
            content: content
        });
        
        if (res && res.ok) {
            showToast('Message envoyé', 'success');
            return res;
        } else {
            const errMsg = res?.message || res?.error || 'Erreur lors de l\'envoi du message';
            showToast(errMsg, 'error');
            return res;
        }
    }

    async function getListingDetails(listingId) {
        if (!listingId) return null;
        const res = await window.KelFonciaAPI.get('listings_get', { id: listingId });
        return (res && res.ok) ? res : null;
    }

    function openContactModal(ownerId, ownerName, ownerEmail, listingId, listingTitle) {
        // Check if modal already exists
        let modal = document.getElementById('contact-modal-overlay');
        if (modal) modal.remove();

        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.id = 'contact-modal-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5000;
            opacity: 0;
            animation: fadeIn 0.3s ease-out forwards;
        `;

        // Create modal content
        const modalContent = document.createElement('div');
        modalContent.style.cssText = `
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.3s ease-out;
        `;

        const initials = (ownerName || ownerEmail || 'U').split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        const recipientColor = '#007bff';

        modalContent.innerHTML = `
            <div style="padding: 30px; border-bottom: 1px solid #e0e6ed;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="color: var(--bleu-pro); margin: 0;">Contacter le propriétaire</h3>
                    <button id="contact-modal-close" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--gris-moyen); padding: 0;">✕</button>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 50px; height: 50px; background: ${recipientColor}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                        ${initials}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--bleu-pro);">${ownerName || ownerEmail}</div>
                        <div style="font-size: 0.9rem; color: var(--gris-moyen);">${ownerName ? ownerEmail : 'Propriétaire'}</div>
                    </div>
                </div>
            </div>
            <div style="padding: 30px;">
                <form id="contact-form">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; color: var(--bleu-pro); margin-bottom: 8px;">Sujet du message</label>
                        <input type="text" name="sujet" value="${listingTitle ? 'À propos de: ' + listingTitle : ''}" readonly style="width: 100%; padding: 12px; border: 1px solid #e0e6ed; border-radius: 8px; background: #f8fafd; color: var(--gris-moyen);">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; color: var(--bleu-pro); margin-bottom: 8px;">Votre message</label>
                        <textarea name="message" placeholder="Écrivez votre message..." required style="width: 100%; padding: 12px; border: 1px solid #e0e6ed; border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 0.95rem; min-height: 150px; resize: vertical;"></textarea>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button type="button" id="contact-modal-cancel" style="flex: 1; padding: 12px; border: 1px solid #e0e6ed; background: white; color: var(--gris-moyen); border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                            Annuler
                        </button>
                        <button type="submit" style="flex: 1; padding: 12px; background: var(--or); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-paper-plane" style="margin-right: 6px;"></i>Envoyer
                        </button>
                    </div>
                </form>
            </div>
        `;

        overlay.appendChild(modalContent);
        document.body.appendChild(overlay);

        // Add animation styles if not already present
        if (!document.getElementById('contact-modal-styles')) {
            const style = document.createElement('style');
            style.id = 'contact-modal-styles';
            style.textContent = `
                @keyframes slideUp {
                    from { transform: translateY(30px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }

        // Close button handlers
        const closeBtn = modalContent.querySelector('#contact-modal-close');
        const cancelBtn = modalContent.querySelector('#contact-modal-cancel');
        
        const closeModal = () => {
            overlay.style.opacity = '0';
            setTimeout(() => overlay.remove(), 300);
        };

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });

        // Form submission
        const form = modalContent.querySelector('#contact-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const messageInput = form.querySelector('textarea[name="message"]');
            const message = messageInput.value.trim();
            
            if (!message) {
                showToast('Veuillez écrire un message', 'error');
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Envoi en cours...';

            // Create conversation first
            const convRes = await initiateConversation(ownerId, listingId, ownerName, listingTitle);
            
            if (convRes && convRes.ok && convRes.id) {
                // Send the message
                const msgRes = await sendMessage(convRes.id, message);
                
                if (msgRes && msgRes.ok) {
                    showToast('Message envoyé avec succès!', 'success');
                    closeModal();
                } else {
                    showToast('Message créé mais erreur d\'envoi', 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Envoyer';
                }
            } else {
                showToast('Erreur lors de la création de la conversation', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Envoyer';
            }
        });
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
        requestKyc,
        initiateConversation,
        sendMessage,
        getListingDetails,
        openContactModal
    };

})();
