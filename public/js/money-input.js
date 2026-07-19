(function () {
    const persianDigits = '۰۱۲۳۴۵۶۷۸۹';

    const MONEY_FIELD_NAMES = /^(price|sale_price|amount|cost|service_cost|unit_price|min_price|max_price|subtotal|total)$|_(price|amount|cost)$/i;

    const EXCLUDED_NAMES = /quantity|stock|percent|tax|sort_order|page|limit|id$/i;

    function getWordsUrl() {
        const meta = document.querySelector('meta[name="money-words-url"]');
        return meta?.content || '';
    }

    function toEnglishDigits(value) {
        let normalized = String(value)
            .replace(/[۰-۹]/g, (d) => String(persianDigits.indexOf(d)))
            .replace(/,/g, '')
            .trim();

        if (normalized === '') {
            return '';
        }

        // PHP decimal casts render as "700300000.00" — stripping all non-digits would append fractional digits.
        const parsed = Math.floor(Number(normalized));
        if (!Number.isFinite(parsed) || parsed < 0) {
            return '';
        }

        return String(parsed);
    }

    function formatAmount(digits) {
        if (!digits) {
            return '';
        }

        return parseInt(digits, 10).toLocaleString('en-US');
    }

    function fieldBaseName(name) {
        if (!name) {
            return '';
        }

        return name.replace(/\[\d+\]/g, '').replace(/\[\]$/g, '');
    }

    function isMoneyField(input) {
        if (!input || input.tagName !== 'INPUT') {
            return false;
        }

        if (input.dataset.moneyInput === 'false') {
            return false;
        }

        if (input.dataset.moneyInput !== undefined || input.dataset.moneyField !== undefined) {
            return true;
        }

        const base = fieldBaseName(input.getAttribute('name') || '');

        if (!base || EXCLUDED_NAMES.test(base)) {
            return false;
        }

        return MONEY_FIELD_NAMES.test(base);
    }

    function ensureWordsElement(input) {
        if (input.dataset.moneyWords) {
            return document.querySelector(input.dataset.moneyWords);
        }

        const base = input.id || fieldBaseName(input.name || 'money');
        const wordsId = 'money-words-' + base.replace(/[^a-z0-9]+/gi, '-');
        let wordsEl = document.getElementById(wordsId);

        if (!wordsEl) {
            wordsEl = document.createElement('p');
            wordsEl.id = wordsId;
            wordsEl.className = 'money-input-words text-xs font-bold text-slate-500 mt-1 min-h-[1.25rem]';
            input.insertAdjacentElement('afterend', wordsEl);
        }

        input.dataset.moneyWords = '#' + wordsEl.id;

        return wordsEl;
    }

    function bindMoneyInput(input) {
        if (!input || input.dataset.moneyBound === '1' || !isMoneyField(input)) {
            return;
        }

        input.dataset.moneyBound = '1';
        input.type = 'text';
        input.inputMode = 'numeric';
        input.autocomplete = 'off';

        if (!input.dataset.moneyWordsUrl) {
            input.dataset.moneyWordsUrl = getWordsUrl();
        }

        const wordsEl = ensureWordsElement(input);

        const sync = () => {
            const digits = toEnglishDigits(input.value);
            input.dataset.rawValue = digits;
            input.value = formatAmount(digits);

            const wordsUrl = input.dataset.moneyWordsUrl || getWordsUrl();

            if (!wordsEl || !wordsUrl) {
                return;
            }

            if (!digits || digits === '0') {
                wordsEl.textContent = '';
                return;
            }

            clearTimeout(input._moneyWordsTimer);
            input._moneyWordsTimer = setTimeout(async () => {
                try {
                    const res = await fetch(`${wordsUrl}?amount=${encodeURIComponent(digits)}`, {
                        headers: { Accept: 'application/json' },
                    });
                    if (!res.ok) {
                        return;
                    }
                    const data = await res.json();
                    wordsEl.textContent = data.words || '';
                } catch (_) {
                    /* ignore */
                }
            }, 300);
        };

        input.addEventListener('input', sync);
        input.addEventListener('blur', sync);

        if (input.dataset.rawValue && !input.value) {
            input.value = formatAmount(input.dataset.rawValue);
        }
        sync();
    }

    function normalizeFormMoneyFields(form) {
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        form.querySelectorAll('input[data-money-bound="1"]').forEach((input) => {
            const raw = input.dataset.rawValue ?? toEnglishDigits(input.value);
            input.value = raw;
            input.type = 'text';
        });
    }

    window.initMoneyInputs = function (root) {
        const scope = root || document;
        scope.querySelectorAll('input').forEach((input) => {
            if (isMoneyField(input)) {
                bindMoneyInput(input);
            }
        });
        scope.querySelectorAll('[data-money-input]').forEach(bindMoneyInput);
    };

    document.addEventListener('DOMContentLoaded', () => {
        window.initMoneyInputs();

        document.querySelectorAll('form').forEach((form) => {
            form.addEventListener('submit', () => normalizeFormMoneyFields(form), true);
        });
    });

    window.MoneyInput = {
        init: window.initMoneyInputs,
        bind: bindMoneyInput,
        parse: toEnglishDigits,
        normalizeForm: normalizeFormMoneyFields,
    };
})();
