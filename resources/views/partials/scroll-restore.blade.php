<?php
    $parsScrollRestore = $parsScrollRestore ?? [];
    $normalizeScrollPath = static function (string $path): string {
        $path = trim($path, '/');
        if (preg_match('#^automation/(service-orders|repairs)/(\d+)$#', $path, $m)) {
            return 'automation/order/'.$m[2];
        }

        return $path;
    };

    $rsParam = request()->query('_rs');
    $bootY = (int) (is_array($rsParam) ? end($rsParam) : ($rsParam ?? 0));
    $bootMeta = session('restore_scroll_meta', []);
    if (! is_array($bootMeta)) {
        $bootMeta = [];
    }
    if ($bootY < 1 && ! empty($parsScrollRestore['y'])) {
        $bootY = (int) $parsScrollRestore['y'];
    }
    if ($bootY < 1) {
        $bootY = (int) session('restore_scroll_y', 0);
    }
    $bootMode = (string) ($parsScrollRestore['mode'] ?? session('restore_scroll_mode', 'main'));
    $bootScrollPayload = [
        'y' => $bootY,
        'path' => $normalizeScrollPath(request()->path()),
        'mode' => $bootMode,
        'anchorTop' => (int) ($parsScrollRestore['anchorTop'] ?? $bootMeta['anchorTop'] ?? 0),
        'formAction' => (string) ($parsScrollRestore['formAction'] ?? $bootMeta['formAction'] ?? ''),
        'rowId' => (string) ($parsScrollRestore['rowId'] ?? $bootMeta['rowId'] ?? ''),
        'rowRelTop' => (int) ($parsScrollRestore['rowRelTop'] ?? $bootMeta['rowRelTop'] ?? 0),
    ];
?>
@if($bootY > 0)
<script type="application/json" id="pars-scroll-boot">@json($bootScrollPayload)</script>
@endif
<script>
(function () {
    'use strict';
    if (window.__parsScrollV2) return;
    window.__parsScrollV2 = true;

    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    var lastKnownY = 0;
    var lastKnownMode = 'main';
    var submitSnapshotY = 0;
    var submitSnapshotMode = 'main';
    var restoreLocked = false;
    var restoreAttempts = 0;
    var maxRestoreAttempts = 6;

    function pathKey() {
        var p = location.pathname.replace(/^\/+/, '');
        var m = p.match(/^automation\/(?:service-orders|repairs)\/(\d+)$/);
        if (m) return 'automation/order/' + m[1];
        return p;
    }

    function storageKey() {
        return 'parsScrollRestore:' + pathKey();
    }

    function root() {
        return document.querySelector('.admin-main');
    }

    function scrollMetrics() {
        var el = root();
        var mainY = el ? el.scrollTop : 0;
        var winY = window.scrollY || document.documentElement.scrollTop || 0;
        var mainMax = el ? Math.max(0, el.scrollHeight - el.clientHeight) : 0;
        var winMax = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
        var mode = 'main';

        if (!el || mainMax < 2) {
            mode = winMax > 2 ? 'win' : 'main';
            return { y: mode === 'win' ? winY : mainY, mainY: mainY, winY: winY, mainMax: mainMax, winMax: winMax, mode: mode };
        }

        return { y: mainY, mainY: mainY, winY: winY, mainMax: mainMax, winMax: winMax, mode: mode };
    }

    function readState() {
        try {
            var raw = sessionStorage.getItem(storageKey());
            if (!raw) return null;
            return JSON.parse(raw);
        } catch (e) {
            return null;
        }
    }

    function writeState(y, extra) {
        if (y < 1) return null;
        lastKnownY = y;
        var metrics = scrollMetrics();
        var mode = (extra && extra.mode) || metrics.mode;
        lastKnownMode = mode;
        var state = Object.assign({ y: y, path: pathKey(), mode: mode, t: Date.now() }, extra || {});
        try {
            var json = JSON.stringify(state);
            sessionStorage.setItem(storageKey(), json);
            document.cookie = 'pars_scroll_restore=' + encodeURIComponent(json) + ';path=/;max-age=120;SameSite=Lax';
        } catch (e) {}
        return state;
    }

    function persistedState() {
        var state = readState();
        if (state && state.y > 0) return state;
        try {
            var boot = document.getElementById('pars-scroll-boot');
            if (boot) {
                var data = JSON.parse(boot.textContent);
                if (data && data.y > 0) return data;
            }
        } catch (e) {}
        try {
            var rs = parseInt(new URLSearchParams(location.search).get('_rs') || '0', 10);
            if (rs > 0) return { y: rs, mode: lastKnownMode };
        } catch (e) {}
        return lastKnownY > 0 ? { y: lastKnownY, mode: lastKnownMode } : null;
    }

    function saveNow() {
        if (restoreLocked) return 0;
        var m = scrollMetrics();
        if (m.y > 0) writeState(m.y, { mode: m.mode });
        return m.y;
    }

    function anchorViewportTop(target, mode) {
        if (!target) return 0;
        var el = root();
        if (mode === 'win' || !el) return target.getBoundingClientRect().top;
        return target.getBoundingClientRect().top - el.getBoundingClientRect().top;
    }

    function findFormByAction(action) {
        if (!action) return null;
        var targetPath = String(action).split('#')[0];
        try { targetPath = new URL(targetPath, location.origin).pathname; } catch (e) {}
        var forms = document.querySelectorAll('form[action]');
        for (var i = 0; i < forms.length; i++) {
            var raw = forms[i].getAttribute('action') || forms[i].action || '';
            var formPath = raw.split('#')[0];
            try { formPath = new URL(formPath, location.origin).pathname; } catch (e) {}
            if (raw === action || formPath === targetPath || raw.endsWith(action) || action.endsWith(raw)) {
                return forms[i];
            }
        }
        return null;
    }

    function applyY(y, mode) {
        if (y < 1) return 0;
        var applied = 0;
        var el = root();
        var metrics = scrollMetrics();
        var useMain = mode === 'main' || (!mode && metrics.mainMax > 1);
        var useWin = mode === 'win' || (!mode && metrics.mainMax < 2);

        if (useMain && el && metrics.mainMax > 1) {
            el.scrollTop = Math.min(Math.round(y), metrics.mainMax);
            applied = el.scrollTop;
        }
        if (useWin && applied < 1) {
            window.scrollTo(0, Math.round(y));
            applied = window.scrollY || document.documentElement.scrollTop || 0;
        }
        return applied;
    }

    function fineTuneAnchor(state) {
        if (!state || typeof state.anchorTop !== 'number') return 0;
        var anchorTarget = null;
        if (state.rowId) anchorTarget = document.getElementById(state.rowId);
        if (!anchorTarget && state.formAction) {
            var form = findFormByAction(state.formAction);
            anchorTarget = form ? (form.closest('[data-scroll-section]') || form) : null;
        }
        if (!anchorTarget) return 0;

        var mode = state.mode || scrollMetrics().mode;
        var delta = Math.round(anchorViewportTop(anchorTarget, mode)) - state.anchorTop;
        if (Math.abs(delta) < 2) return 0;

        if (mode === 'win') window.scrollBy(0, delta);
        else if (root()) root().scrollTop += delta;
        return delta;
    }

    function scrollToSection(sectionId) {
        var target = document.getElementById(sectionId);
        var el = root();
        if (!target) return false;
        if (el) {
            var top = el.scrollTop + (target.getBoundingClientRect().top - el.getBoundingClientRect().top);
            el.scrollTop = Math.max(0, Math.round(top));
            return true;
        }
        target.scrollIntoView({ block: 'start' });
        return true;
    }

    function restore() {
        if (restoreLocked || restoreAttempts >= maxRestoreAttempts) return 0;
        restoreAttempts++;

        var state = persistedState();
        if (!state || state.y < 1) return 0;

        var el = root();
        var mode = state.mode || 'main';
        var applied = 0;

        if (el && state.rowId) {
            var row = document.getElementById(state.rowId);
            if (row && typeof state.rowRelTop === 'number') {
                var rel = row.getBoundingClientRect().top - el.getBoundingClientRect().top;
                var rowY = el.scrollTop + rel - state.rowRelTop;
                applied = applyY(rowY, mode);
            }
        }

        if (applied < 1) {
            applied = applyY(state.y, mode);
            fineTuneAnchor(state);
        }

        var max = el ? Math.max(0, el.scrollHeight - el.clientHeight) : 0;
        if (applied >= Math.min(state.y, max) - 12 || restoreAttempts >= maxRestoreAttempts) {
            restoreLocked = true;
        }

        return applied;
    }

    function restoreWithFallback() {
        if (restoreLocked) return;

        var sectionId = @json(session('scroll_to'));
        if (!sectionId && location.hash) {
            sectionId = location.hash.slice(1);
        }

        if (sectionId && document.getElementById(sectionId)) {
            if (scrollToSection(sectionId)) {
                restoreLocked = true;
                return;
            }
        }

        var applied = restore();
        if (applied < 1 && sectionId) {
            scrollToSection(sectionId);
        }
    }

    function injectForm(form) {
        if (!form || form.tagName !== 'FORM') return;
        if (form.target === '_blank' || form.hasAttribute('data-no-scroll-restore')) return;
        if ((form.getAttribute('method') || 'get').toLowerCase() !== 'post') return;

        var m = scrollMetrics();
        var y = Math.max(submitSnapshotY, m.y, lastKnownY);
        var mode = submitSnapshotY > 0 ? submitSnapshotMode : m.mode;
        var row = form.closest('tr[id]');
        var scrollSection = form.closest('[data-scroll-section]');
        var scrollToInput = form.querySelector('input[name="scroll_to"]');
        var anchorEl = row || scrollSection || (scrollToInput && scrollToInput.value ? document.getElementById(scrollToInput.value) : null) || form;
        var rowMeta = {
            rowId: row ? row.id : (scrollSection ? scrollSection.id : ''),
            rowRelTop: (function () {
                var r = root();
                var relTarget = row || scrollSection;
                if (!r || !relTarget) return 0;
                return Math.round(relTarget.getBoundingClientRect().top - r.getBoundingClientRect().top);
            })(),
            mode: mode,
            anchorTop: Math.round(anchorViewportTop(anchorEl, mode)),
            formAction: form.getAttribute('action') || form.action || ''
        };

        if (y > 0) writeState(y, rowMeta);

        ['_scroll_y', '_scroll_path', '_scroll_row_id', '_scroll_row_rel', '_scroll_mode', '_scroll_anchor', '_scroll_form'].forEach(function (name) {
            var input = form.querySelector('input[name="' + name + '"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                form.appendChild(input);
            }
        });

        form.querySelector('input[name="_scroll_y"]').value = String(Math.round(y));
        form.querySelector('input[name="_scroll_path"]').value = pathKey();
        form.querySelector('input[name="_scroll_row_id"]').value = rowMeta.rowId || '';
        form.querySelector('input[name="_scroll_row_rel"]').value = String(rowMeta.rowRelTop || 0);
        form.querySelector('input[name="_scroll_mode"]').value = mode;
        form.querySelector('input[name="_scroll_anchor"]').value = String(rowMeta.anchorTop || 0);
        form.querySelector('input[name="_scroll_form"]').value = rowMeta.formAction || '';
    }

    function snapshotBeforeSubmit() {
        var m = scrollMetrics();
        submitSnapshotY = m.y;
        submitSnapshotMode = m.mode;
        if (m.y > 0) writeState(m.y, { mode: m.mode });
    }

    document.addEventListener('pointerdown', function (e) {
        var btn = e.target.closest('button[type="submit"], input[type="submit"]');
        if (btn && btn.form) snapshotBeforeSubmit();
    }, true);

    document.addEventListener('submit', function (e) {
        if (e.target && e.target.tagName === 'FORM') {
            snapshotBeforeSubmit();
            injectForm(e.target);
        }
    }, true);

    window.addEventListener('pagehide', saveNow);
    window.addEventListener('beforeunload', saveNow);

    function bindRoot() {
        var el = root();
        if (!el || el.dataset.parsScrollV2) return;
        el.dataset.parsScrollV2 = '1';
        el.addEventListener('scroll', function () {
            clearTimeout(el.__parsScrollSaveTimer);
            el.__parsScrollSaveTimer = setTimeout(saveNow, 120);
        }, { passive: true });
    }

    function boot() {
        bindRoot();
        restoreWithFallback();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    [200, 600, 1200, 2500].forEach(function (ms) {
        setTimeout(restoreWithFallback, ms);
    });

    window.addEventListener('load', restoreWithFallback);

    if (typeof window.hidePageLoader === 'function') {
        var origHide = window.hidePageLoader;
        window.hidePageLoader = function () {
            var r = origHide.apply(this, arguments);
            setTimeout(restoreWithFallback, 50);
            return r;
        };
    }
})();
</script>
