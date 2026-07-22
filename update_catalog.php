<?php
$file = 'resources/views/catalog/index.blade.php';
$content = file_get_contents($file);

// C1: Clear filters
$content = preg_replace('/(<button[^>]*@click="resetFilters"[^>]*>)/', '$1 aria-label="حذف فیلترها"', $content);

// C2: Pagination buttons
$content = preg_replace('/(<button[^>]*@click="[^\"]*prevPage[^\"]*"[^>]*>)/', '$1 aria-label="صفحه قبل"', $content);
$content = preg_replace('/(<button[^>]*@click="[^\"]*nextPage[^\"]*"[^>]*>)/', '$1 aria-label="صفحه بعد"', $content);
$content = preg_replace('/(<button[^>]*@click="goToPage\(page\)"[^>]*)>/', '$1 aria-label="رفتن به صفحه {{ page }}">', $content);

// C3: Checkbox \'for\' attribute
// First find the brands checkbox loop
$content = preg_replace(
    '/<label class="catalog-checkbox">\s*<input type="checkbox" x-model="selectedBrands" :value="brand.id" class="catalog-checkbox__input">/',
    '<label :for="\'brand-\' + brand.id" class="catalog-checkbox">' . "\n" . '                                            <input type="checkbox" :id="\'brand-\' + brand.id" x-model="selectedBrands" :value="brand.id" class="catalog-checkbox__input">',
    $content
);

// C4: Grid/List view buttons
$content = str_replace('@click="viewMode = \'grid\'"', '@click="viewMode = \'grid\'" aria-label="نمایش شبکه‌ای"', $content);
$content = str_replace('@click="viewMode = \'list\'"', '@click="viewMode = \'list\'" aria-label="نمایش لیستی"', $content);

// C5: Debounce for Alpine search
$content = str_replace('x-model="searchQuery"', 'x-model.debounce.500ms="searchQuery"', $content);

// C6: aria-label for sort select
$content = str_replace('<select x-model="sortBy"', '<select x-model="sortBy" aria-label="مرتب‌سازی محصولات"', $content);

file_put_contents($file, $content);
echo "Catalog updated.\n";
