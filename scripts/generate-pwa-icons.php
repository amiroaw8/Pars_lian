<?php

$src = __DIR__ . '/../public/images/pars-lian-logo.png';

if (! is_file($src)) {
    fwrite(STDERR, "Logo not found: {$src}\n");
    exit(1);
}

function makeIcon(string $src, string $dest, int $size): void
{
    $img = @imagecreatefrompng($src);
    if (! $img) {
        copy($src, $dest);

        return;
    }

    $w = imagesx($img);
    $h = imagesy($img);
    $side = min($w, $h);
    $sx = (int) (($w - $side) / 2);
    $sy = (int) (($h - $side) / 2);
    $out = imagecreatetruecolor($size, $size);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
    imagefilledrectangle($out, 0, 0, $size, $size, $transparent);
    imagecopyresampled($out, $img, 0, 0, $sx, $sy, $size, $size, $side, $side);
    imagepng($out, $dest);
}

$dir = __DIR__ . '/../public/assets/images';
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}

makeIcon($src, $dir . '/icon-192x192.png', 192);
makeIcon($src, $dir . '/icon-512x512.png', 512);

echo "PWA icons generated.\n";
