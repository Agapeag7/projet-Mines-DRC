// API helper for KelFoncia AJAX calls
(function(){
    // Map actions to endpoint files in /api.  Compute the base path relative to
    // the current page so the code works even if the project folder name or
    // URL changes (avoids hardcoding '/KelFoncia-DRC').
    const basePath = location.pathname.replace(/\/[^/]*$/, '');
    const BASE_API = basePath + '/api';

    function endpointFor(action) {
        const auth = ['login','register'];
        const listings = ['list','listings_list','create','listings_create','get','listings_get','update','listings_update','delete','listings_delete','toggle_favorite','favorite_toggle'];
        const media = ['upload','media_upload','media_list','list_by_listing','media_get','media_delete','download','media_download'];
        const notifications = ['notifications_list','notifications_mark_read','notifications_create','list','mark_read','create'];
        const conversations = ['conversation_create','message_send','message_list','conversation_list','send','create','dashboard_messages','conversation_mark_read','message_mark_read'];
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
            const text = await res.text();
            if (text.trim().startsWith('<')) {
                console.error('Non-JSON response received', text);
                return { error: 'non_json_response', raw: text };
            }
            try {
                return JSON.parse(text);
            } catch(jsonErr) {
                console.error('JSON parse failed', jsonErr, text);
                return { error: jsonErr.message, raw: text };
            }
        } catch (e) { return { error: e.message }; }
    }

    async function postFormData(action, formData) {
        const url = endpointFor(action);
        try {
            const res = await fetch(url, { method: 'POST', body: formData, credentials: 'same-origin' });
            const text = await res.text();
            if (text.trim().startsWith('<')) {
                console.error('Non-JSON response received', text);
                return { error: 'non_json_response', raw: text };
            }
            try {
                return JSON.parse(text);
            } catch(jsonErr) {
                console.error('JSON parse failed', jsonErr, text);
                return { error: jsonErr.message, raw: text };
            }
        } catch (e) { return { error: e.message }; }
    }

    async function postJSON(action, obj) {
        const url = endpointFor(action);
        try {
            const res = await fetch(url, { method: 'POST', body: JSON.stringify(obj), headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin' });
            const text = await res.text();
            if (text.trim().startsWith('<')) {
                console.error('Non-JSON response received', text);
                return { error: 'non_json_response', raw: text };
            }
            try {
                return JSON.parse(text);
            } catch(jsonErr) {
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
            const text = await res.text();
            if (text.trim().startsWith('<')) {
                console.error('Non-JSON response received', text);
                return { error: 'non_json_response', raw: text };
            }
            try {
                return JSON.parse(text);
            } catch(jsonErr) {
                console.error('JSON parse failed', jsonErr, text);
                return { error: jsonErr.message, raw: text };
            }
        } catch(e) { return { error: e.message }; }
    }

    window.KelFonciaAPI = { postForm, postFormData, postJSON, get };
})();
