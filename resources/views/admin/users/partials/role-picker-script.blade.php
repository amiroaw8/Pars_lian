@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.role-picker-card input[type="checkbox"]').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            var label = this.closest('.role-picker-card');
            if (label) {
                label.classList.toggle('is-selected', this.checked);
            }
        });

        var label = checkbox.closest('.role-picker-card');
        if (label && checkbox.checked) {
            label.classList.add('is-selected');
        }
    });
});
</script>
@endpush
@endonce
