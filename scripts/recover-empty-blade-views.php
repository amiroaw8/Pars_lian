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
        '/<\?php echo csrf_field\(\);\s*\?>/' => '@csrf',
        '/<\?php if\s*\((.*?)\):\s*\?>/s' => '@if($1)',
        '/<\?php elseif\s*\((.*?)\):\s*\?>/s' => '@elseif($1)',
        '/<\?php else:\s*\?>/' => '@else',
        '/<\?php endif;\s*\?>/' => '@endif',
        '/<\?php foreach\s*\((.*?)\):\s*\?>/s' => '@foreach($1)',
        '/<\?php endforeach;\s*\?>/' => '@endforeach',
    ];

    foreach ($replacements as $pattern => $replacement) {
        $result = preg_replace($pattern, $replacement, $body);
        if ($result === null) {
            return null;
        }
        $body = $result;
    }

    $body = trim($body);

    return strlen($body) >= 20 ? $body : null;
}

$recovered = 0;

foreach (glob($compiledDir . '/*.php') as $compiledFile) {
    $php = file_get_contents($compiledFile);
    if ($php === false || ! preg_match('/PATH (.+?) ENDPATH/s', $php, $match)) {
        continue;
    }

    $path = str_replace('\\', '/', $match[1]);
    if (! str_contains($path, '/resources/views/') || str_contains($path, '/vendor/')) {
        continue;
    }

    $relative = substr($path, strpos($path, '/resources/views/') + strlen('/resources/views/'));
    $target = $projectRoot . '/resources/views/' . $relative;

    if (is_file($target) && filesize($target) > 0) {
        continue;
    }

    $blade = decompileBlade($php);
    if ($blade === null) {
        continue;
    }

    $dir = dirname($target);
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($target, $blade . "\n");
    $recovered++;
    echo 'Recovered empty: ' . $relative . PHP_EOL;
}

echo 'Recovered ' . $recovered . ' empty files' . PHP_EOL;
