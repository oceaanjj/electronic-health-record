/**
 * resources/js/doctor/recent-forms.js
 *
 * Live filtering for the Doctor → Recent Forms page.
 * Changes to type select, patient input, date picker, or pill tabs
 * fetch a fresh results partial via AJAX and swap it in — no full reload.
 */

(function () {
    'use strict';

    // ── State ────────────────────────────────────────────────────────────────
    let currentPage    = 1;
    let abortController = null;
    let debounceTimer   = null;

    // ── Element refs (set in init) ───────────────────────────────────────────
    let wrap, typeSelect, patientInput, dateInput, totalText, pillLinks, clearBtn;

    // ── Bootstrap ────────────────────────────────────────────────────────────
    function init() {
        wrap         = document.getElementById('rf-results-wrap');
        typeSelect   = document.getElementById('rf-type');
        patientInput = document.getElementById('rf-patient');
        dateInput    = document.getElementById('rf-date');
        totalText    = document.getElementById('rf-total-text');
        pillLinks    = document.querySelectorAll('.rf-pill');
        clearBtn     = document.getElementById('rf-clear-btn');

        if (!wrap || !typeSelect) return; // not on this page

        // Read initial page from rendered meta
        const meta = wrap.querySelector('#rf-meta');
        if (meta) currentPage = parseInt(meta.dataset.page) || 1;

        // ── Listeners ────────────────────────────────────────────────────────
        typeSelect.addEventListener('change', () => {
            currentPage = 1;
            syncPillsToSelect();
            fetchResults();
        });

        patientInput.addEventListener('input', () => {
            currentPage = 1;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchResults, 380);
        });

        dateInput.addEventListener('change', () => {
            currentPage = 1;
            fetchResults();
        });

        // Pill tabs — intercept navigation, update select, fetch
        pillLinks.forEach(pill => {
            pill.addEventListener('click', e => {
                e.preventDefault();
                const type = pill.dataset.pillType;
                typeSelect.value = type;
                currentPage = 1;
                syncPillsToSelect();
                fetchResults();
            });
        });

        // Clear-filter button
        if (clearBtn) {
            clearBtn.addEventListener('click', e => {
                e.preventDefault();
                typeSelect.value   = 'all';
                patientInput.value = '';
                dateInput.value    = '';
                currentPage        = 1;
                syncPillsToSelect();
                fetchResults();
            });
        }

        // Prevent the filter form from doing a full-page submit
        const form = document.getElementById('rf-filter-form');
        if (form) {
            form.addEventListener('submit', e => {
                e.preventDefault();
                currentPage = 1;
                fetchResults();
            });
        }

        // Pagination — delegated on the wrap (re-rendered on every fetch)
        wrap.addEventListener('click', e => {
            const link = e.target.closest('.rf-page-link[data-ajax-page]');
            if (!link) return;
            e.preventDefault();
            currentPage = parseInt(link.dataset.ajaxPage) || 1;
            fetchResults(true); // scroll to top of list
        });

        // Browser back / forward
        window.addEventListener('popstate', e => {
            if (e.state && e.state.rfFilters) {
                const s = e.state.rfFilters;
                typeSelect.value   = s.type    || 'all';
                patientInput.value = s.patient || '';
                dateInput.value    = s.date    || '';
                currentPage        = s.page    || 1;
                syncPillsToSelect();
                fetchResults(false, true); // skipPush=true
            }
        });
    }

    // ── Core fetch ───────────────────────────────────────────────────────────
    function fetchResults(scrollToWrap = false, skipPush = false) {
        // Cancel any in-flight request
        if (abortController) abortController.abort();
        abortController = new AbortController();

        const params = buildParams();

        if (!skipPush) pushState(params);

        showLoading();

        const url = window.location.pathname + '?' + params.toString();

        fetch(url, {
            signal: abortController.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrf(),
            },
        })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.text();
            })
            .then(html => {
                wrap.innerHTML = html;
                updateTotalBadge();
                updateClearButton();
                if (scrollToWrap) {
                    wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(err => {
                if (err.name !== 'AbortError') {
                    hideLoading();
                    console.error('[recent-forms] fetch error:', err);
                }
            });
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    function buildParams() {
        const p = new URLSearchParams();
        const type    = typeSelect.value   || 'all';
        const patient = patientInput.value.trim();
        const date    = dateInput.value;

        if (type    && type !== 'all') p.set('type',    type);
        if (patient)                   p.set('patient', patient);
        if (date)                      p.set('date',    date);
        if (currentPage > 1)           p.set('page',    currentPage);
        return p;
    }

    function pushState(params) {
        const state = {
            rfFilters: {
                type:    typeSelect.value,
                patient: patientInput.value.trim(),
                date:    dateInput.value,
                page:    currentPage,
            },
        };
        const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        history.pushState(state, '', url);
    }

    function updateTotalBadge() {
        const meta = wrap.querySelector('#rf-meta');
        if (!meta || !totalText) return;
        const n = parseInt(meta.dataset.total) || 0;
        totalText.textContent = n.toLocaleString() + ' record' + (n !== 1 ? 's' : '');
    }

    function updateClearButton() {
        if (!clearBtn) return;
        const hasFilters =
            (typeSelect.value && typeSelect.value !== 'all') ||
            patientInput.value.trim() ||
            dateInput.value;
        clearBtn.style.display = hasFilters ? '' : 'none';
    }

    function syncPillsToSelect() {
        const active = typeSelect.value || 'all';
        pillLinks.forEach(pill => {
            const type      = pill.dataset.pillType;
            const isActive  = type === active;
            const color     = pill.dataset.pillColor;

            pill.style.backgroundColor = isActive ? color : '#fff';
            pill.style.color           = isActive ? '#fff' : '#374151';
            pill.style.borderColor     = isActive ? color  : '#E5E7EB';
            pill.style.fontFamily      = isActive
                ? "'Alte Haas Grotesk Bold', arial"
                : "'Alte Haas Grotesk', arial";
        });
    }

    function showLoading() {
        wrap.style.position   = 'relative';
        wrap.style.minHeight  = '120px';

        // Remove stale overlay if any
        const old = wrap.querySelector('.rf-loading-overlay');
        if (old) old.remove();

        const overlay = document.createElement('div');
        overlay.className = 'rf-loading-overlay';
        overlay.style.cssText = [
            'position:absolute', 'inset:0', 'z-index:10',
            'background:rgba(255,255,255,0.65)',
            'backdrop-filter:blur(2px)',
            'display:flex', 'align-items:center', 'justify-content:center',
            'border-radius:1rem',
        ].join(';');
        overlay.innerHTML = `
            <div style="display:flex;flex-direction:column;align-items:center;gap:10px">
                <svg style="width:36px;height:36px;animation:rf-spin 0.8s linear infinite" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9" stroke="#d1fae5" stroke-width="3"/>
                    <path d="M12 3a9 9 0 0 1 9 9" stroke="#15803d" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <span style="font-size:13px;color:#6b7280;font-family:'Alte Haas Grotesk',arial">Updating…</span>
            </div>`;
        wrap.appendChild(overlay);

        // Inject spin keyframes once
        if (!document.getElementById('rf-spin-style')) {
            const s = document.createElement('style');
            s.id = 'rf-spin-style';
            s.textContent = '@keyframes rf-spin { to { transform: rotate(360deg); } }';
            document.head.appendChild(s);
        }
    }

    function hideLoading() {
        const overlay = wrap && wrap.querySelector('.rf-loading-overlay');
        if (overlay) overlay.remove();
    }

    function getCsrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    // ── Run ──────────────────────────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
