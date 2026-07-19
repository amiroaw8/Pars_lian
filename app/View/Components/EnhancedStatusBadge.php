<?php

namespace App\View\Components;

use App\Enums\ServiceOrderStatus;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EnhancedStatusBadge extends Component
{
    public string $status;

    public string $variant;

    public bool $animated;

    /**
     * Create a new component instance.
     */
    public function __construct(ServiceOrderStatus|string|null $status = null, string $variant = 'auto', bool $animated = false)
    {
        if ($status instanceof ServiceOrderStatus) {
            $this->status = $status->value;
        } else {
            $this->status = $status ?? 'registered';
        }
        
        $this->variant = $variant;
        $this->animated = $animated;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.enhanced-status-badge');
    }
}
