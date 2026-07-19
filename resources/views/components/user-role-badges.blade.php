@props([
    'user',
    'size' => 'sm',
])

@php
    $roles = $user->roles;
    $sizeClass = $size === 'xs'
        ? '!text-[10px] !px-2 !py-0.5'
        : '!text-xs !px-2.5 !py-1';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-1.5 max-w-[14rem]']) }}>
    @forelse($roles as $role)
        <x-enhanced-status-badge
            :status="$role->name"
            :label="\App\Support\RoleLabels::label($role->name)"
            :class="$sizeClass"
        />
    @empty
        <x-enhanced-status-badge status="customer" class="{{ $sizeClass }}" />
    @endforelse
</div>
