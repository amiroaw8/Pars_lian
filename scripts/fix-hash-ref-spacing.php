<?php

$viewsDir = __DIR__.'/../resources/views';
$pattern = '/<x-hash-ref :value="\s*([^"]+?)\s*"\s*\/>/';

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $path = $file->getPathname();
    $content = file_get_contents($path);
    $updated = preg_replace($pattern, '<x-hash-ref :value="$1" />', $content);

    if ($updated !== null && $updated !== $content) {
        file_put_contents($path, $updated);
        echo 'Fixed: '.str_replace($viewsDir.DIRECTORY_SEPARATOR, '', $path).PHP_EOL;
    }
}

echo "Done.\n";
