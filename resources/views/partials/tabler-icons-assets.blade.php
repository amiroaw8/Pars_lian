<?php
    $tablerFontUrl = asset('vendor/tabler-icons/fonts/tabler-icons.woff2') . '?v3.36.0';
    $tablerCssUrl = asset('vendor/tabler-icons/tabler-icons.min.css') . '?v3.36.1';
?>
<link rel="preload" href="{{ $tablerFontUrl }}" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="{{ $tablerCssUrl }}">
