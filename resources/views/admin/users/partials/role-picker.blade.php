@once
@push('styles')
@include('admin.users.partials.role-picker-styles')
@endpush
@endonce

@include('admin.users.partials.role-picker-grid', [
    'roles' => $roles,
    'selectedRoles' => $selectedRoles ?? [],
    'locked' => $locked ?? false,
    'hiddenRoles' => $hiddenRoles ?? [],
])

@include('admin.users.partials.role-picker-script')
