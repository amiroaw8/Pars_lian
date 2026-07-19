(function () {
    'use strict';

    const NAME_LABELS = {
        name: 'نام',
        phone: 'شماره موبایل',
        mobile: 'شماره موبایل',
        email: 'ایمیل',
        password: 'رمز عبور',
        password_confirmation: 'تکرار رمز عبور',
        address: 'آدرس',
        amount: 'مبلغ',
        cost: 'هزینه',
        price: 'قیمت',
        sale_price: 'قیمت فروش',
        unit_price: 'قیمت واحد',
        quantity: 'تعداد',
        title: 'عنوان',
        description: 'توضیحات',
        parent_id: 'دسته مادر',
        customer_id: 'مشتری',
        device_type: 'نوع دستگاه',
        device_model: 'مدل دستگاه',
        device_variant: 'جزئیات دستگاه',
        service_type: 'نوع سرویس',
        receiver_name: 'نام تحویل‌گیرنده',
        receiver_phone: 'تلفن تحویل‌گیرنده',
        fault: 'ایراد دستگاه',
        debt_amount: 'میزان بدهی',
        debt_reason: 'دلیل بدهی',
        code: 'کد تأیید',
        token: 'توکن',
        category: 'دسته‌بندی',
        technician_id: 'تکنسین',
        customer_name: 'نام مشتری',
        customer_phone: 'تلفن مشتری',
        customer_address: 'آدرس مشتری',
        notes: 'یادداشت',
        accessories: 'لوازم همراه',
    };

    function fieldBaseName(name) {
        if (!name) {
            return '';
        }
        return name.replace(/\[\d+\]/g, '').replace(/\[\]/g, '').replace(/.*\[/, '').replace(/\]$/, '');
    }

    function resolveLabel(el) {
        if (el.dataset.label) {
            return el.dataset.label.trim();
        }

        const id = el.getAttribute('id');
        if (id) {
            const label = document.querySelector('label[for="' + id.replace(/"/g, '\\"') + '"]');
            if (label) {
                return label.textContent.replace(/\*/g, '').trim();
            }
        }

        const group = el.closest('.form-group-modern, .form-group, .money-field-wrap, .money-input-wrap');
        const groupLabel = group && group.querySelector('label, .form-label-modern, .form-label');
        if (groupLabel) {
            return groupLabel.textContent.replace(/\*/g, '').trim();
        }

        const base = fieldBaseName(el.getAttribute('name') || '');
        if (base && NAME_LABELS[base]) {
            return NAME_LABELS[base];
        }

        const placeholder = el.getAttribute('placeholder');
        if (placeholder) {
            return placeholder.replace(/\(.*\)/g, '').trim();
        }

        return 'این فیلد';
    }

    function persianMessage(el) {
        const label = resolveLabel(el);
        const quoted = '«' + label + '»';
        const v = el.validity;

        if (v.valueMissing) {
            if (el.tagName === 'SELECT') {
                return 'لطفاً ' + quoted + ' را انتخاب کنید.';
            }
            return 'لطفاً ' + quoted + ' را پر کنید.';
        }

        if (v.typeMismatch) {
            if (el.type === 'email') {
                return 'لطفاً یک ایمیل معتبر وارد کنید.';
            }
            if (el.type === 'url') {
                return 'لطفاً یک آدرس اینترنتی معتبر وارد کنید.';
            }
            return 'فرمت ' + quoted + ' صحیح نیست.';
        }

        if (v.patternMismatch) {
            return 'فرمت ' + quoted + ' صحیح نیست.';
        }

        if (v.tooShort) {
            return quoted + ' باید حداقل ' + el.minLength + ' کاراکتر باشد.';
        }

        if (v.tooLong) {
            return quoted + ' نباید بیشتر از ' + el.maxLength + ' کاراکتر باشد.';
        }

        if (v.rangeUnderflow) {
            return 'مقدار ' + quoted + ' کمتر از حد مجاز (' + el.min + ') است.';
        }

        if (v.rangeOverflow) {
            return 'مقدار ' + quoted + ' بیشتر از حد مجاز (' + el.max + ') است.';
        }

        if (v.stepMismatch) {
            return 'مقدار ' + quoted + ' معتبر نیست.';
        }

        if (v.badInput) {
            return 'مقدار واردشده برای ' + quoted + ' نامعتبر است.';
        }

        return 'مقدار ' + quoted + ' معتبر نیست.';
    }

    function bindValidationMessages(el) {
        if (!el || el.dataset.validationFaBound === '1') {
            return;
        }
        if (!(el instanceof HTMLInputElement || el instanceof HTMLSelectElement || el instanceof HTMLTextAreaElement)) {
            return;
        }
        if (el.type === 'hidden' || el.type === 'submit' || el.type === 'button' || el.disabled) {
            return;
        }

        el.dataset.validationFaBound = '1';

        el.addEventListener('invalid', function () {
            this.setCustomValidity(persianMessage(this));
        });

        const clear = function () {
            this.setCustomValidity('');
        };

        el.addEventListener('input', clear);
        el.addEventListener('change', clear);
    }

    function bindForm(form) {
        if (!(form instanceof HTMLFormElement) || form.dataset.validationFaForm === '1') {
            return;
        }
        if (form.hasAttribute('novalidate')) {
            return;
        }

        form.dataset.validationFaForm = '1';
        form.querySelectorAll('input, select, textarea').forEach(bindValidationMessages);
    }

    function init(root) {
        const scope = root || document;
        scope.querySelectorAll('form').forEach(bindForm);
        scope.querySelectorAll('input, select, textarea').forEach(bindValidationMessages);
    }

    document.addEventListener('DOMContentLoaded', function () {
        init();
    });

    document.addEventListener('focusin', function (e) {
        bindValidationMessages(e.target);
    }, true);

    window.FormValidationFa = { init: init, bind: bindValidationMessages, messageFor: persianMessage };
})();
