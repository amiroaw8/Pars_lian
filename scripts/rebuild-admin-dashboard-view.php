<?php

$dashboardPath = __DIR__ . '/../resources/views/admin/dashboard.blade.php';
$partialPath = __DIR__ . '/dashboard-charts.partial.blade.php';

$lines = file($dashboardPath, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    fwrite(STDERR, "Cannot read dashboard view\n");
    exit(1);
}

$marker = '<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 animate-slide-up" style="animation-delay: 0.2s;">';
$cutIndex = null;

foreach ($lines as $index => $line) {
    if (str_contains($line, $marker)) {
        $cutIndex = $index;
        break;
    }
}

if ($cutIndex === null) {
    fwrite(STDERR, "Corrupted marker not found; dashboard may already be fixed.\n");
    exit(0);
}

$head = array_slice($lines, 0, $cutIndex);
if (($head[0] ?? '') === "@section('content')") {
    array_unshift(
        $head,
        "@extends('layouts.admin')",
        '',
        "@section('title', 'داشبورد مدیریت سیستم - پارس لیان')",
        ''
    );
}

$tail = file($partialPath, FILE_IGNORE_NEW_LINES);
if ($tail === false) {
    fwrite(STDERR, "Cannot read dashboard partial\n");
    exit(1);
}

$content = implode(PHP_EOL, array_merge($head, $tail)) . PHP_EOL;
file_put_contents($dashboardPath, $content);

echo 'Rebuilt admin dashboard view (' . count($head) + count($tail) . " lines)\n";
