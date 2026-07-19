<?php

$currentBase = __DIR__ . '/../resources/views';
$finalBase = 'C:/laragon/www/pars-lianfinal/pars-lian/resources/views';

$skipFromFinal = [
    'admin/files/index.blade.php',
    'admin/products/partials/inventory-link-card.blade.php',
    'admin/products/partials/quick-category-modal-scripts.blade.php',
    'admin/products/partials/quick-category-modal.blade.php',
    'admin/users/partials/role-picker-grid.blade.php',
    'admin/users/partials/role-picker-script.blade.php',
    'admin/users/partials/role-picker-styles.blade.php',
    'components/ltr-num.blade.php',
    'components/print-company-header.blade.php',
    'device-types/_parent-select.blade.php',
    'components/jalali-date.blade.php',
    'components/hash-ref.blade.php',
    'components/money-field.blade.php',
    'customers/show.blade.php',
    'admin/dashboard.blade.php',
];

function relPath(string $base, string $file): string
{
    return str_replace('\\', '/', substr($file, strlen($base) + 1));
}

function isDecompiled(string $content): bool
{
    $trim = ltrim($content);

    return str_starts_with($trim, '<?php $component')
        || str_starts_with($trim, '<?php echo $__env')
        || preg_match('/Illuminate\\\\View\\\\AnonymousComponent::resolve/', $content) === 1
        || preg_match('/@if\(isset\(\$component\)\)\s*\{/', $content) === 1
        || preg_match('/App\\\\View\\\\Components\\\\.+::resolve\(/', $content) === 1
        || str_contains($content, '<?php echo csrf_field(); ?>');
}

$decompiled = [];
$restored = [];
$noFinal = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($currentBase, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php' || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $rel = relPath($currentBase, $file->getPathname());
    $content = file_get_contents($file->getPathname()) ?: '';

    if ($content === '' || ! isDecompiled($content)) {
        continue;
    }

    $decompiled[] = $rel;

    if (in_array($rel, $skipFromFinal, true)) {
        continue;
    }

    $source = $finalBase . '/' . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    if (! is_file($source)) {
        $noFinal[] = $rel;
        continue;
    }

    copy($source, $file->getPathname());
    $restored[] = $rel;
}

echo 'decompiled=' . count($decompiled) . PHP_EOL;
echo 'restored_from_final=' . count($restored) . PHP_EOL;
echo 'no_final=' . count($noFinal) . PHP_EOL;

if ($decompiled) {
    echo PHP_EOL . 'Decompiled files:' . PHP_EOL;
    foreach ($decompiled as $f) {
        echo "  - {$f}\n";
    }
}

if ($noFinal) {
    echo PHP_EOL . 'No final copy:' . PHP_EOL;
    foreach ($noFinal as $f) {
        echo "  - {$f}\n";
    }
}
