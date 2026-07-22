<?php

namespace App\View\Components;

use App\Support\BrandLogo as BrandLogoAsset;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

class BrandLogo extends Component
{
    public string $heightClass;

    public string $maxWidthClass;

    public string $src;

    public string $loading;

    public bool $highPriority;

    public function __construct(string $size = 'md', string $mode = 'web')
    {
        [$this->heightClass, $this->maxWidthClass] = self::resolveSizeClasses($size);
        $this->src = $mode === 'print' ? BrandLogoAsset::dataUri() : BrandLogoAsset::url();
        $this->loading = $mode === 'web' ? 'eager' : 'lazy';
        $this->highPriority = $mode === 'web';
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function resolveSizeClasses(string $size): array
    {
        return match ($size) {
            'sm' => ['h-9', 'max-w-[200px]'],
            'lg' => ['h-14', 'max-w-[280px]'],
            'xl' => ['h-16', 'max-w-[320px]'],
            'print' => ['h-[52px]', 'max-w-[240px]'],
            'admin' => ['h-9', 'max-w-[140px]'],
            default => ['h-11', 'max-w-[240px]'],
        };
    }

    public function imageAttributes(): ComponentAttributeBag
    {
        $attributes = $this->attributes->merge([
            'class' => $this->heightClass . ' w-auto ' . $this->maxWidthClass . ' rounded-xl object-contain object-right',
            'loading' => $this->loading,
            'width' => '320',
            'height' => '175',
            'decoding' => 'async',
        ]);

        if ($this->highPriority) {
            $attributes = $attributes->merge(['fetchpriority' => 'high']);
        }

        return $attributes;
    }

    public function render(): View|Closure|string
    {
        return view('components.brand-logo-image');
    }
}
