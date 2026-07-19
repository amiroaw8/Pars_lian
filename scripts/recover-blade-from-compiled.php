<?php

$compiledDir = __DIR__ . '/../storage/framework/views';
$projectRoot = realpath(__DIR__ . '/..');

function decompileBlade(string $php): ?string
{
    if (! preg_match('/PATH (.+?) ENDPATH/s', $php)) {
        return null;
    }

    $body = preg_replace('/<\?php echo \$__env->make\(.+$/s', '', $php);
    $body = preg_replace('/<\?php \/\*\*PATH.+ENDPATH\*\*\/\s*\?>\s*$/s', '', $body);

    if (str_contains($body, '$__env->startComponent(')) {
        $parts = preg_split('/\?>\s*/', $body, 2);
        $body = $parts[1] ?? '';
    }

    $replacements = [
        '/<\?php \$__env->startSection\(\'([^\']+)\',\s*(.*?)\);\s*\?>/s' => '@section(\'$1\', $2)',
        '/<\?php \$__env->startSection\(\'([^\']+)\'\);\s*\?>/' => '@section(\'$1\')',
        '/<\?php \$__env->stopSection\(\);\s*\?>/' => '@endsection',
        '/<\?php echo e\((.*?)\);\s*\?>/s' => '{{ $1 }}',
        '/<\?php echo \$__env->yieldContent\(\'([^\']+)\'\);\s*\?>/' => '@yield(\'$1\')',
        '/<\?php if\s*\((.*?)\):\s*\?>/s' => '@if($1)',
        '/<\?php elseif\s*\((.*?)\):\s*\?>/s' => '@elseif($1)',
        '/<\?php else:\s*\?>/' => '@else',
        '/<\?php endif;\s*\?>/' => '@endif',
        '/<\?php foreach\s*\((.*?)\):\s*\?>/s' => '@foreach($1)',
        '/<\?php endforeach;\s*\?>/' => '@endforeach',
        '/<\?php for\s*\((.*?)\):\s*\?>/s' => '@for($1)',
        '/<\?php endfor;\s*\?>/' => '@endfor',
    ];

    foreach ($replacements as $pattern => $replacement) {
        $body = preg_replace($pattern, $replacement, $body);
    }

    $body = trim($body);

    if (strlen($body) < 20) {
        return null;
    }

    return $body;
}

$recovered = 0;
$skipped = 0;

foreach (glob($compiledDir . '/*.php') as $compiledFile) {
    $php = file_get_contents($compiledFile);
    if (! preg_match('/PATH (.+?) ENDPATH/s', $php, $match)) {
        continue;
    }

    $path = str_replace('\\', '/', $match[1]);
    if (! str_contains($path, '/resources/views/') || str_contains($path, '/vendor/')) {
        continue;
    }

    $relative = substr($path, strpos($path, '/resources/views/') + strlen('/resources/views/'));
    $target = $projectRoot . '/resources/views/' . $relative;

    $blade = decompileBlade($php);
    if ($blade === null) {
        $skipped++;
        continue;
    }

    $dir = dirname($target);
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($target, $blade . "\n");
    $recovered++;
    echo 'Recovered: ' . $relative . PHP_EOL;
}

echo 'Done. Recovered ' . $recovered . ', skipped ' . $skipped . PHP_EOL;
