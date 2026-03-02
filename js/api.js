// API helper for KelFoncia AJAX calls
(function(){
    // Map actions to endpoint files in /api
    const BASE_API = '/KelFoncia-DRC/api';

    function endpointFor(action) {
        const auth = ['login','register'];
        const listings = ['list','listings_list','create','listings_create','get','listings_get','update','listings_update','delete','listings_delete','toggle_favorite','favorite_toggle'];
        const media = ['upload','media_upload','media_list','list_by_listing','media_get','media_delete'];
        const notifications = ['notifications_list','notifications_mark_read','notifications_create','list','mark_read','create'];
        const conversations = ['conversation_create','message_send','message_list','conversation_list','send','create'];
        const kyc = ['kyc_request','request'];
        if (auth.includes(action)) return `${BASE_API}/auth.php?action=${encodeURIComponent(action)}`;
        if (media.includes(action)) return `${BASE_API}/media.php?action=${encodeURIComponent(action)}`;
        if (notifications.includes(action)) return `${BASE_API}/notifications.php?action=${encodeURIComponent(action)}`;
        if (conversations.includes(action)) return `${BASE_API}/conversations.php?action=${encodeURIComponent(action)}`;
        if (kyc.includes(action)) return `${BASE_API}/kyc.php?action=${encodeURIComponent(action)}`;
        // default to listings
        return `${BASE_API}/listings.php?action=${encodeURIComponent(action)}`;
    }

    async function postForm(action, form) {
        const url = endpointFor(action);
        const fd = new FormData(form);
        try {
            const res = await fetch(url, { method: 'POST', body: fd, credentials: 'same-origin' });
            try {
                return await res.json();
            } catch(jsonErr) {
                const text = await res.text();
                console.error('JSON parse failed', jsonErr, text);
                return { error: jsonErr.message, raw: text };
            }
        } catch (e) { return { error: e.message }; }
    }

    async function postJSON(action, obj) {
        const url = endpointFor(action);
        try {
            const res = await fetch(url, { method: 'POST', body: JSON.stringify(obj), headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin' });
            try {
                return await res.json();
            } catch(jsonErr) {
                const text = await res.text();
                console.error('JSON parse failed', jsonErr, text);
                return { error: jsonErr.message, raw: text };
            }
        } catch (e) { return { error: e.message }; }
    }

    async function get(action, params={}){
        const url = endpointFor(action);
        const u = new URL(location.origin + url);
        Object.keys(params).forEach(k => u.searchParams.set(k, params[k]));
        try {
            const res = await fetch(u.toString(), { credentials: 'same-origin' });
            try {
                return await res.json();
            } catch(jsonErr) {
                const text = await res.text();
                console.error('JSON parse failed', jsonErr, text);
                return { error: jsonErr.message, raw: text };
            }
        } catch(e) { return { error: e.message }; }
    }

    window.KelFonciaAPI = { postForm, postJSON, get };
})();
