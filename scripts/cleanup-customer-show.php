<?php

$path = __DIR__ . '/../resources/views/customers/show.blade.php';
$lines = file($path, FILE_IGNORE_NEW_LINES);
$out = [];
$skip = 0;

foreach ($lines as $line) {
    if ($skip > 0) {
        if (str_contains($line, '<x-') || str_contains($line, '@endif</span>')) {
            if (str_contains($line, '@endif</span>')) {
                $out[] = '</span>';
            } else {
                $out[] = $line;
            }
            $skip = 0;
        }
        continue;
    }

    if (preg_match('/<\?php if \(isset\(\$component\)\)/', $line)) {
        $skip = 1;
        continue;
    }

    if (str_contains($line, '__componentOriginal') || str_contains($line, '__attributesOriginal')) {
        continue;
    }

    if (str_contains($line, 'renderComponent()')) {
        continue;
    }

    if (preg_match('/^<\?php \$attributes = \$__attributesOriginal/', $line)) {
        continue;
    }

    if (preg_match('/^<\?php \$component = \$__componentOriginal/', $line)) {
        continue;
    }

    if (str_contains($line, 'Arr::toCssClasses')) {
        $line = '@php echo \Illuminate\Support\Arr::toCssClasses([';
    }

    $out[] = $line;
}

$content = implode("\n", $out);
$content = preg_replace('/@endif\s*\n\s*@if\(isset\(\$__attributesOriginal[^)]+\)\)\s*\n\s*@endif\s*\n\s*@if\(isset\(\$__componentOriginal[^)]+\)\)\s*\n\s*@endif/s', '', $content);
$content = preg_replace("/\n{3,}/", "\n\n", $content);

file_put_contents($path, $content."\n");
echo "Cleaned show.blade.php\n";
