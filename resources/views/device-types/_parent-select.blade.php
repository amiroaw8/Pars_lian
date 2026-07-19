@props([
    'id' => 'parent_id',
    'name' => 'parent_id',
    'options' => [],
    'selected' => null,
    'emptyLabel' => '— سطح اول (بدون دسته مادر) —',
])

<div class="relative">
    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors z-0">
        <i class="ti ti-chevron-down text-xl"></i>
    </div>
    <select
        name="{{ $name }}"
        id="{{ $id }}"
        {{ $attributes->merge(['class' => 'form-control-modern appearance-none pr-12 pl-4 w-full relative z-10 focus:ring-4 focus:ring-blue-500/10']) }}
    >
        <option value="">{{ $emptyLabel }}</option>
        @foreach($options as $optionId => $label)
            <option value="{{ $optionId }}" @selected((string) $selected === (string) $optionId)>{{ $label }}</option>
        @endforeach
    </select>
</div>
