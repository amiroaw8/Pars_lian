<?php

$compiledPath = __DIR__ . '/../storage/framework/views/6d9ac2a2dccb55279a6cbd81a568ae4c.php';
$outputPath = __DIR__ . '/../resources/views/customers/show.blade.php';
$lines = file($compiledPath, FILE_IGNORE_NEW_LINES);
$out = [];
$skip = 0;
$inClassComponent = false;
$slotName = null;

foreach ($lines as $line) {
    if ($skip > 0) {
        if (str_contains($line, 'renderComponent()') || (str_contains($line, '$component->render()'))) {
            $skip = 0;
        }
        continue;
    }

    if (preg_match("/AnonymousComponent::resolve\(\['view' => 'components\.([^']+)','data' => \[(.*)\]\]/", $line, $m)) {
        $tag = str_replace('.', '-', $m[1]);
        $attrs = [];
        if (preg_match_all("/'([^']+)'\s*=>\s*([^,\]]+)/", $m[2], $props, PREG_SET_ORDER)) {
            foreach ($props as $prop) {
                $key = $prop[1];
                $val = trim($prop[2]);
                $attrs[] = ':'.$key.'="'.$val.'"';
            }
        }
        $out[] = '<x-'.$tag.' '.implode(' ', $attrs).' />';
        $skip = 1;
        continue;
    }

    if (preg_match("/App\\\\View\\\\Components\\\\(\w+)::resolve\(\['([^']+)' => ([^\]]+)\]/", $line, $m)) {
        $tag = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $m[1]));
        $out[] = '<x-'.$tag.' :'.$m[2].'="'.$m[3].'" />';
        $skip = 1;
        continue;
    }

    if (preg_match("/App\\\\View\\\\Components\\\\(\w+)::resolve\(\[\]/", $line, $m)) {
        $tag = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $m[1]));
        $out[] = '<x-'.$tag.' striped hover responsive>';
        $inClassComponent = true;
        $skip = 1;
        continue;
    }

    if (str_contains($line, '$__env->slot(')) {
        if (preg_match("/slot\('([^']+)'/", $line, $m)) {
            $slotName = $m[1];
            $out[] = '<x-slot name="'.$slotName.'">';
        }
        continue;
    }

    if (str_contains($line, '$__env->endSlot()')) {
        $out[] = '</x-slot>';
        if ($slotName === 'rows' && $inClassComponent) {
            $out[] = '</x-enhanced-table>';
            $inClassComponent = false;
        }
        $slotName = null;
        continue;
    }

    foreach ([
        '/<\?php \$__env->startSection\(\'([^\']+)\',\s*(.*?)\);\s*\?>/' => '@section(\'$1\', $2)',
        '/<\?php \$__env->startSection\(\'([^\']+)\'\);\s*\?>/' => '@section(\'$1\')',
        '/<\?php \$__env->stopSection\(\);\s*\?>/' => '@endsection',
        '/<\?php echo csrf_field\(\);\s*\?>/' => '@csrf',
        '/<\?php echo e\((.*?)\);\s*\?>/' => '{{ $1 }}',
        '/<\?php if\s*\((.*?)\):\s*\?>/' => '@if($1)',
        '/<\?php elseif\s*\((.*?)\):\s*\?>/' => '@elseif($1)',
        '/<\?php else:\s*\?>/' => '@else',
        '/<\?php endif;\s*\?>/' => '@endif',
        '/<\?php \$__currentLoopData = (.*?); \$__env->addLoop\(\$__currentLoopData\); foreach\(\$__currentLoopData as (.*?)\): \$__env->incrementLoopIndices\(\); \$loop = \$__env->getLastLoop\(\);\s*\?>/' => '@foreach($1 as $2)',
        '/<\?php endforeach; \$__env->popLoop\(\); \$loop = \$__env->getLastLoop\(\);\s*\?>/' => '@endforeach',
    ] as $pattern => $replacement) {
        $line = preg_replace($pattern, $replacement, $line);
    }

    if (preg_match('/<\?php (?:if \(isset\(\$__componentOriginal|if \(isset\(\$attributes\)|\$component = Illuminate|\$component->withName|\$attributes = \$attributes->except|if \(\$component->shouldRender|__env->startComponent|unset\(\$__componentOriginal|unset\(\$__attributesOriginal)/', $line)) {
        continue;
    }

    if (trim($line) === '<?php' || trim($line) === '?>') {
        continue;
    }

    if (str_contains($line, 'echo $__env->make(')) {
        continue;
    }

    if (str_contains($line, '/**PATH')) {
        continue;
    }

    $out[] = $line;
}

$blade = "@extends('layouts.admin')\n\n".implode("\n", $out)."\n";
file_put_contents($outputPath, $blade);
touch($outputPath, filemtime($compiledPath) - 10);
echo 'Wrote '.$outputPath.' ('.strlen($blade)." bytes)\n";
