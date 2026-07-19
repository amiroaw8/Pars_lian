<?php
    use App\Support\AdminSidebar;
    $sidebarSections = AdminSidebar::sections(auth()->user());
?>

<div class="sidebar-menu">
    <div class="sidebar-search">
        <div class="sidebar-search-wrap">
            <input type="search" id="adminSidebarSearch" class="sidebar-search-input" placeholder="جستجو در منو..." autocomplete="off">
            <i class="ti ti-search text-sm"></i>
        </div>
    </div>

    <?php $__currentLoopData = $sidebarSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="menu-group {{ !empty($section['footer']) ? 'mt-auto border-t border-white/10 pt-4' : '' }}">
            @if(!empty($section['label']))
                <span class="group-label">{{ $section['label'] }}</span>
            @endif

            <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $linkClass = 'nav-link-modern ' . ($item['active'] ?? false ? 'active' : '');
                    if (!empty($item['class'])) {
                        $linkClass .= ' ' . $item['class'];
                    }
                    $onclick = !empty($item['confirm']) ? "return confirm('" . e($item['confirm']) . "')" : null;
                ?>
                <a href="{{ $item['href'] }}"
                   class="{{ $linkClass }}"
                   data-search-label="{{ $item['label'] }}"
                   @if($onclick) onclick="{{ $onclick }}" @endif>
                    <i class="ti ti-{{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
