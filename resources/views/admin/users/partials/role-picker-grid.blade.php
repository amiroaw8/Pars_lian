@php
    use App\Support\RoleLabels;

    $locked = $locked ?? false;
    $hiddenRoles = $hiddenRoles ?? [];
@endphp

<div class="role-picker-grid">
    @foreach($hiddenRoles as $hiddenRole)
        <input type="hidden" name="role[]" value="{{ $hiddenRole }}">
    @endforeach

    @foreach($roles as $role)
        @php
            $meta = RoleLabels::meta($role->name);
            $roleName = $role->name;
            $isSelected = in_array($roleName, $selectedRoles, true);
            $accent = RoleLabels::accent($roleName);
        @endphp
        <label
            class="role-picker-card {{ $locked ? 'is-locked' : '' }} {{ $isSelected ? 'is-selected' : '' }}"
            style="--role-accent: {{ $accent }}"
        >
            <input
                type="checkbox"
                @unless($locked) name="role[]" @endunless
                value="{{ $roleName }}"
                class="sr-only"
                {{ $isSelected ? 'checked' : '' }}
                @if($locked) disabled @endif
            >
            <span class="role-picker-check" aria-hidden="true">
                <i class="ti ti-circle-check-filled"></i>
            </span>
            <span class="role-picker-icon">
                <i class="ti {{ $meta['icon'] }}"></i>
            </span>
            <span class="role-picker-text">{{ $meta['label'] }}</span>
            @if($locked && $isSelected)
                <span class="role-picker-badge">
                    <i class="ti ti-lock"></i>
                    فعال
                </span>
            @endif
        </label>
    @endforeach
</div>
