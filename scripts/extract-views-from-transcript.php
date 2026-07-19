<?php

$files = [
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
    'automation/pos/sales.blade.php',
];

$transcripts = glob('C:/Users/User/.cursor/projects/c-laragon-www-pars-lian/agent-transcripts/*/*.jsonl') ?: [];
$found = [];

foreach ($transcripts as $transcript) {
    foreach (file($transcript) as $line) {
        $j = json_decode($line, true);
        if (! $j) {
            continue;
        }

        foreach ($j['message']['content'] ?? [] as $block) {
            if (($block['type'] ?? '') !== 'tool_use' || ($block['name'] ?? '') !== 'Write') {
                continue;
            }

            $path = str_replace('\\', '/', $block['input']['path'] ?? '');
            $path = preg_replace('#.*resources/views/#', '', $path);
            if (in_array($path, $files, true)) {
                $found[$path] = $block['input']['contents'] ?? '';
            }
        }
    }
}

$base = __DIR__ . '/../resources/views';
foreach ($files as $file) {
    if (! isset($found[$file]) || trim($found[$file]) === '') {
        echo "MISSING: {$file}\n";
        continue;
    }

    $dest = $base . '/' . str_replace('/', DIRECTORY_SEPARATOR, $file);
    $dir = dirname($dest);
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($dest, $found[$file]);
    echo "RESTORED: {$file} (" . strlen($found[$file]) . " bytes)\n";
}
