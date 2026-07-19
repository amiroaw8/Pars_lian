<?php

/**
 * One-off script: replace #{{ $var }} with <x-hash-ref :value="$var" />
 * Skips money-field/money-input (CSS selector IDs).
 */

$viewsDir = __DIR__.'/../resources/views';
$skipFiles = ['money-field.blade.php', 'money-input.blade.php', 'hash-ref.blade.php'];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

$pattern = '/#\{\{([^}]+)\}\}/';

foreach ($iterator as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    if (in_array($file->getFilename(), $skipFiles, true)) {
        continue;
    }

    $path = $file->getPathname();
    $content = file_get_contents($path);
    $updated = preg_replace($pattern, '<x-hash-ref :value="$1" />', $content);

    if ($updated !== null && $updated !== $content) {
        file_put_contents($path, $updated);
        echo 'Updated: '.str_replace($viewsDir.DIRECTORY_SEPARATOR, '', $path).PHP_EOL;
    }
}

echo "Done.\n";
