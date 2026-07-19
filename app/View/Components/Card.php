<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public string $title;

    public string $variant;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title = '', string $variant = 'default')
    {
        $this->title = $title;
        $this->variant = $variant;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'blade'
@php
    $variantClasses = [
        'default' => 'card-default',
        'primary' => 'card-primary',
        'success' => 'card-success',
        'warning' => 'card-warning',
        'danger' => 'card-danger'
    ];
    
    $class = $variantClasses[$variant] ?? 'card-default';
@endphp

<div class="card {{ $class }}">
    @if($title)
        <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
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
