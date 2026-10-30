/**
 * Smart Hospital — CKEditor iframe theme sync.
 *
 * WHY: The CKEditor editing area is a sandboxed <iframe> with its own document.
 * It loads plugins/ckeditor/contents.css but CANNOT read the parent page's CSS
 * custom properties (--surface, --ink, ...). Theme Studio has multiple dark
 * presets that map to different body classes (nightshift => body.variant-a brown,
 * midnight/oled => body.variant-b navy/black) plus custom palettes, so a static
 * `body.variant-b`-keyed rule in contents.css can never match every dark theme.
 *
 * FIX: read the parent's *live computed* theme tokens and inject them as a
 * <style> into the iframe document. Theme-agnostic — works for light mode and
 * every preset, and re-applies on a live Theme Studio switch. Same approach used
 * for the cross-origin Stripe checkout iframe.
 *
 * Loaded globally in layout/footer.php right after ckeditor.js. It does NOT
 * define CKEDITOR.editorConfig, so it never clobbers the per-editor config.
 */
(function () {
    if (typeof CKEDITOR === 'undefined') { return; }

    function applyTheme(editor) {
        try {
            if (!editor || !editor.document) { return; }
            var doc = editor.document.$;                       // native iframe document
            if (!doc) { return; }
            var head = doc.head || doc.getElementsByTagName('head')[0];
            if (!head) { return; }

            var cs = getComputedStyle(document.body);
            function tok(name, fallback) {
                var v = cs.getPropertyValue(name);
                return (v && v.trim()) ? v.trim() : fallback;
            }

            var surface = tok('--surface', '#ffffff');
            var ink     = tok('--ink', '#333333');
            var ink2    = tok('--ink-2', ink);
            var border  = tok('--border', '#cccccc');
            var link    = tok('--link', '') || tok('--accent', '#0782c1');

            // !important so this beats both contents.css `body{background:#fff}`
            // and its `body.variant-b{...}` rule, whichever applied.
            var css =
                'html,body{background-color:' + surface + ' !important;color:' + ink + ' !important;}' +
                'a{color:' + link + ' !important;}' +
                'hr{border:0 !important;border-top:1px solid ' + border + ' !important;}' +
                'blockquote{border-color:' + border + ' !important;color:' + ink2 + ' !important;}' +
                'pre{background:' + surface + ' !important;color:' + ink2 + ' !important;}' +
                'table td,table th{border-color:' + border + ' !important;}' +
                '::selection{background:' + link + ';color:' + surface + ';}';

            var id = 'sh-cke-theme';
            var old = doc.getElementById(id);
            if (old && old.parentNode) { old.parentNode.removeChild(old); }
            var st = doc.createElement('style');
            st.id = id;
            st.type = 'text/css';
            st.appendChild(doc.createTextNode(css));
            head.appendChild(st);
        } catch (e) { /* never let theming break the editor */ }
    }

    // First paint + re-paint whenever the editable iframe is rebuilt
    // (e.g. returning from Source view, which re-creates the document).
    CKEDITOR.on('instanceReady', function (ev) {
        var editor = ev.editor;
        applyTheme(editor);
        editor.on('contentDom', function () { applyTheme(editor); });
    });

    // Live Theme Studio switch: body class / data-preset / inline vars change
    // while editors are open — re-sync every live instance.
    if (window.MutationObserver) {
        var pending = false;
        var mo = new MutationObserver(function () {
            if (pending) { return; }
            pending = true;
            setTimeout(function () {
                pending = false;
                for (var name in CKEDITOR.instances) {
                    if (CKEDITOR.instances.hasOwnProperty(name)) {
                        applyTheme(CKEDITOR.instances[name]);
                    }
                }
            }, 0);
        });
        mo.observe(document.body, { attributes: true, attributeFilter: ['class', 'data-preset', 'style'] });
    }
})();
