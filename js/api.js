// API helper for KelFoncia AJAX calls
(function(){
    const BASE = '/KelFoncia-DRC/kel.class.php';

    async function postForm(action, form) {
        const url = BASE + '?action=' + encodeURIComponent(action);
        const fd = new FormData(form);
        try {
            const res = await fetch(url, { method: 'POST', body: fd, credentials: 'same-origin' });
            return await res.json();
        } catch (e) { return { error: e.message }; }
    }

    async function postJSON(action, obj) {
        const url = BASE + '?action=' + encodeURIComponent(action);
        try {
            const res = await fetch(url, { method: 'POST', body: JSON.stringify(obj), headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin' });
            return await res.json();
        } catch (e) { return { error: e.message }; }
    }

    async function get(action, params={}){
        const u = new URL(location.origin + BASE);
        u.searchParams.set('action', action);
        Object.keys(params).forEach(k => u.searchParams.set(k, params[k]));
        try {
            const res = await fetch(u.toString(), { credentials: 'same-origin' });
            return await res.json();
        } catch(e) { return { error: e.message }; }
    }

    window.KelFonciaAPI = { postForm, postJSON, get };
})();
