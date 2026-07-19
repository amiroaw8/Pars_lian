<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public string $variant;

    public string $href;

    public string $type;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $variant = 'primary',
        string $href = '#',
        string $type = 'link'
    ) {
        $this->variant = $variant;
        $this->href = $href;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'blade'
@php
    $variantClasses = [
        'primary' => 'btn-primary',
        'success' => 'btn-success', 
        'warning' => 'btn-warning',
        'danger' => 'btn-danger'
    ];
    
    $class = $variantClasses[$variant] ?? 'btn-primary';
@endphp

@if($type === 'link')
    <a href="{{ $href }}" class="btn {{ $class }}">
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" class="btn {{ $class }}">
        {{ $slot }}
    </button>
@endif
blade;
    }
}
