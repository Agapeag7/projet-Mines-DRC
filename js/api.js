// API helper for KelFoncia AJAX calls
(function(){
    // Map actions to endpoint files in /api
    const BASE_API = '/KelFoncia-DRC/api';

    function endpointFor(action) {
        // auth actions -> auth.php, others -> listings.php
        const auth = ['login','register'];
        if (auth.includes(action)) return `${BASE_API}/auth.php?action=${encodeURIComponent(action)}`;
        return `${BASE_API}/listings.php?action=${encodeURIComponent(action)}`;
    }

    async function postForm(action, form) {
        const url = endpointFor(action);
        const fd = new FormData(form);
        try {
            const res = await fetch(url, { method: 'POST', body: fd, credentials: 'same-origin' });
            return await res.json();
        } catch (e) { return { error: e.message }; }
    }

    async function postJSON(action, obj) {
        const url = endpointFor(action);
        try {
            const res = await fetch(url, { method: 'POST', body: JSON.stringify(obj), headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin' });
            return await res.json();
        } catch (e) { return { error: e.message }; }
    }

    async function get(action, params={}){
        const url = endpointFor(action);
        const u = new URL(location.origin + url);
        Object.keys(params).forEach(k => u.searchParams.set(k, params[k]));
        try {
            const res = await fetch(u.toString(), { credentials: 'same-origin' });
            return await res.json();
        } catch(e) { return { error: e.message }; }
    }

    window.KelFonciaAPI = { postForm, postJSON, get };
})();
