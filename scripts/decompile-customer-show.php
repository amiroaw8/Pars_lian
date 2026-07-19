<?php

function decompileCompiledView(string $php): string
{
    $php = preg_replace('/<\?php echo \$__env->make\(.+$/s', '', $php);
    $php = preg_replace('/<\?php \/\*\*PATH.+ENDPATH\*\*\/\s*\?>\s*$/s', '', $php);

    // Convert anonymous components to blade tags
    $php = preg_replace_callback(
        '/<\?php \$component = Illuminate\\\\View\\\\AnonymousComponent::resolve\(\[\'view\' => \'components\.([^\']+)\',\'data\' => \[(.*?)\]\].*?\?>\s*<\?php \$component->withName\(\'[^\']+\'\);\s*\?>.*?(?:<\?php echo \$__env->renderComponent\(\);\s*\?>|<\?php \$component->render\(\);\s*\?>)/s',
        function (array $m): string {
            $name = str_replace('.', '-', $m[1]);
            $props = [];
            if (preg_match_all('/\'([^\']+)\'\s*=>\s*([^,\]]+)/', $m[2], $propMatches, PREG_SET_ORDER)) {
                foreach ($propMatches as $prop) {
                    $key = $prop[1];
                    $val = trim($prop[2]);
                    if ($val === 'true') {
                        $props[] = ':'.$key.'="true"';
                    } elseif ($val === 'false') {
                        $props[] = ':'.$key.'="false"';
                    } else {
                        $props[] = ':'.$key.'="'.$val.'"';
                    }
                }
            }

            return '<x-'.$name.' '.implode(' ', $props).' />';
        },
        $php
    );

    // Remove leftover component boilerplate lines
    $php = preg_replace('/<\?php if \(isset\(\$component\)\) \{ \$__componentOriginal[^}]+\}\s*\?>\s*/', '', $php);
    $php = preg_replace('/<\?php if \(isset\(\$attributes\)\) \{ \$__attributesOriginal[^}]+\}\s*\?>\s*/', '', $php);
    $php = preg_replace('/<\?php \$component = Illuminate\\\\View\\\\AnonymousComponent::resolve\(.+$/m', '', $php);
    $php = preg_replace('/<\?php \$component->withName\(.+$/m', '', $php);
    $php = preg_replace('/<\?php if \(\$component->shouldRender\(\)\): \?>.+$/m', '', $php);
    $php = preg_replace('/<\?php \$__env->startComponent\(.+$/m', '', $php);
    $php = preg_replace('/<\?php \$attributes = \$attributes->except\(.+$/m', '', $php);
    $php = preg_replace('/<\?php if \(isset\(\$attributes\) && \$attributes instanceof Illuminate\\\\View\\\\ComponentAttributeBag\): \?>.+$/m', '', $php);
    $php = preg_replace('/<\?php \$component->withAttributes\(\[(.*?)\]\);\s*\?>/s', '', $php);
    $php = preg_replace('/<\?php echo \$__env->renderComponent\(\);\s*\?>/', '', $php);
    $php = preg_replace('/<\?php endif;\s*\?>\s*<\?php if \(isset\(\$__componentOriginal[^}]+\}\s*\?>/s', '', $php);
    $php = preg_replace('/<\?php unset\(\$__componentOriginal[^;]+;\s*\?>/', '', $php);

    $php = preg_replace('/<\?php \$__env->startSection\(\'([^\']+)\',\s*(.*?)\);\s*\?>/s', '@section(\'$1\', $2)', $php);
    $php = preg_replace('/<\?php \$__env->startSection\(\'([^\']+)\'\);\s*\?>/', '@section(\'$1\')', $php);
    $php = preg_replace('/<\?php \$__env->stopSection\(\);\s*\?>/', '@endsection', $php);

    $php = preg_replace('/<\?php echo e\((.*?)\);\s*\?>/s', '{{ $1 }}', $php);
    $php = preg_replace('/<\?php echo \$__env->yieldContent\(\'([^\']+)\'\);\s*\?>/', '@yield(\'$1\')', $php);

    $php = preg_replace('/<\?php if\s*\((.*?)\):\s*\?>/s', '@if($1)', $php);
    $php = preg_replace('/<\?php elseif\s*\((.*?)\):\s*\?>/s', '@elseif($1)', $php);
    $php = preg_replace('/<\?php else:\s*\?>/', '@else', $php);
    $php = preg_replace('/<\?php endif;\s*\?>/', '@endif', $php);

    $php = preg_replace('/<\?php \$__currentLoopData = (.*?); \$__env->addLoop\(\$__currentLoopData\); foreach\(\$__currentLoopData as (.*?)\): \$__env->incrementLoopIndices\(\); \$loop = \$__env->getLastLoop\(\);\s*\?>/s', '@foreach($1 as $2)', $php);
    $php = preg_replace('/<\?php endforeach; \$__env->popLoop\(\); \$loop = \$__env->getLastLoop\(\);\s*\?>/', '@endforeach', $php);

    $php = preg_replace('/<\?php \$__env->startComponent\(\'([^\']+)\',\s*([^,]+),\s*\[(.*?)\]\);\s*\?>/s', '<x-$1 $3>', $php);
    $php = preg_replace('/<\?php \$__env->slot\(\'([^\']+)\'\);\s*\?>/', '<x-slot name="$1">', $php);
    $php = preg_replace('/<\?php \$__env->endSlot\(\);\s*\?>/', '</x-slot>', $php);

    $php = preg_replace("/\n{3,}/", "\n\n", trim($php));

    return $php;
}

$compiled = file_get_contents(__DIR__ . '/../storage/framework/views/6d9ac2a2dccb55279a6cbd81a568ae4c.php');
$blade = decompileCompiledView($compiled);
$blade = "@extends('layouts.admin')\n\n@section('title', 'مشاهده مشتری - ' . \$customer->name)\n\n" . $blade . "\n";
file_put_contents(__DIR__ . '/../resources/views/customers/show.blade.php', $blade);
echo 'Wrote customers/show.blade.php (' . strlen($blade) . " bytes)\n";
