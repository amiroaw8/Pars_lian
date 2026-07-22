<?php
$file = 'resources/views/shop/index.blade.php';
$content = file_get_contents($file);

$content = str_replace('<h3 class="text-xl font-black text-slate-900">تعمیرات تخصصی</h3>', '<h2 class="text-xl font-black text-slate-900">تعمیرات تخصصی</h2>', $content);
$content = str_replace('<h3 class="text-xl font-black text-slate-900">قطعات اورجینال</h3>', '<h2 class="text-xl font-black text-slate-900">قطعات اورجینال</h2>', $content);
$content = str_replace('<h3 class="text-xl font-black text-slate-900">تحویل اکسپرس</h3>', '<h2 class="text-xl font-black text-slate-900">تحویل اکسپرس</h2>', $content);

$content = preg_replace('/<h4\b([^>]*)>(.*?)<\/h4>/', '<h3$1>$2</h3>', $content);

file_put_contents($file, $content);
echo "Headings updated.\n";
