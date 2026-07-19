<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatusBadge extends Component
{
    public string $status;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $status = null)
    {
        $this->status = $status ?? 'registered';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'blade'
@php
    $statusLabels = [
        'registered' => ['label' => 'ثبت شده', 'class' => 'status-registered'],
        'repairing' => ['label' => 'در حال تعمیر', 'class' => 'status-repairing'],
        'ready' => ['label' => 'آماده تحویل', 'class' => 'status-ready'],
        'delivered' => ['label' => 'تحویل شده', 'class' => 'status-delivered']
    ];
    
    $config = $statusLabels[$status] ?? ['label' => 'نامشخص', 'class' => 'status-unknown'];
@endphp

<span class="status-badge {{ $config['class'] }}">
    {{ $config['label'] }}
</span>
blade;
    }
}
