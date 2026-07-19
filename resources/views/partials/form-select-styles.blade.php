<style>
select.form-control:not(.select2-hidden-accessible),
select.form-control-modern:not(.select2-hidden-accessible) {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 100%;
    min-height: 2.75rem;
    height: auto !important;
    line-height: 1.5;
    padding-top: 0.625rem;
    padding-bottom: 0.625rem;
    padding-right: 1rem;
    padding-left: 2.5rem;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 0.75rem center;
    background-size: 1rem;
    cursor: pointer;
}

select.form-control.min-h-12:not(.select2-hidden-accessible),
select.form-control.h-12:not(.select2-hidden-accessible),
select.form-control-modern.h-12:not(.select2-hidden-accessible) {
    min-height: 3rem;
}

select.form-control-modern.h-11:not(.select2-hidden-accessible) {
    min-height: 2.75rem;
}

select.form-control-modern.h-14:not(.select2-hidden-accessible) {
    min-height: 3.5rem;
}

select.form-control.h-10:not(.select2-hidden-accessible) {
    min-height: 2.5rem;
}

/* Select2 — prevent clipped placeholder/text in RTL admin forms */
.select2-container--default[dir="rtl"] .select2-selection--single {
    min-height: 2.75rem;
    height: auto !important;
    display: flex;
    align-items: center;
    border: 1.5px solid #e2e8f0;
    border-radius: 0.75rem;
}

.select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__rendered {
    line-height: 1.5;
    padding: 0.625rem 1rem 0.625rem 2.5rem;
    width: 100%;
    min-height: 2.75rem;
    display: flex;
    align-items: center;
}

.select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__placeholder {
    line-height: 1.5;
}

.select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow {
    height: 100%;
    left: 0.75rem;
    right: auto;
    top: 0;
    width: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
    margin-left: 0.5rem;
}
</style>
