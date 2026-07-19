<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EnhancedCard extends Component
{
    public string $title;

    public string $variant;

    public bool $animated;

    public bool $glass;

    public bool $gradient;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title = '',
        string $variant = 'default',
        bool $animated = false,
        bool $glass = false,
        bool $gradient = false
    ) {
        $this->title = $title;
        $this->variant = $variant;
        $this->animated = $animated;
        $this->glass = $glass;
        $this->gradient = $gradient;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'blade'
@php
    $variantClasses = [
        'default' => 'card',
        'primary' => 'card card-gradient',
        'success' => 'card card-success',
        'warning' => 'card card-warning',
        'danger' => 'card card-danger',
        'info' => 'card card-info'
    ];
    
    $baseClass = $variantClasses[$variant] ?? 'card';
    
    $additionalClasses = [];
    if ($animated) {
        $additionalClasses[] = 'hover-lift';
    }
    if ($glass) {
        $additionalClasses[] = 'card-glass';
    }
    if ($gradient) {
        $additionalClasses[] = 'card-gradient';
    }
    
    $class = trim($baseClass . ' ' . implode(' ', $additionalClasses));
@endphp

<div class="{{ $class }}">
    @if($title)
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-{{ 
                    $variant == 'primary' ? 'star' : 
                    ($variant == 'success' ? 'check' : 
                    ($variant == 'warning' ? 'alert-triangle' : 
                    ($variant == 'danger' ? 'alert-circle' : 'square'))) 
                }}"></i>
                {{ $title }}
            </h3>
        </div>
    @endif
    
    <div class="card-body">
        {{ $slot }}
    </div>
    
    @if(isset($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
blade;
    }
}
