<?php
$src_hero = 'public/images/pars-lian-logo-hero.png';
$dest_hero = 'public/images/pars-lian-logo-hero.webp';
$maxWidth = 518;

function loadImage(string $path)
{
    $contents = file_get_contents($path);
    if ($contents === false) {
        return false;
    }

    $image = @imagecreatefromstring($contents);
    if ($image !== false) {
        return $image;
    }

    return match (mime_content_type($path)) {
        'image/png' => @imagecreatefrompng($path),
        'image/jpeg' => @imagecreatefromjpeg($path),
        'image/webp' => @imagecreatefromwebp($path),
        default => false,
    };
}

$img = loadImage($src_hero);
if (!$img) {
    die("Failed to open image\n");
}

imagepalettetotruecolor($img);
imagealphablending($img, true);
imagesavealpha($img, true);

$origWidth = imagesx($img);
$origHeight = imagesy($img);

if ($origWidth > $maxWidth) {
    $newWidth = $maxWidth;
    $newHeight = (int) ($origHeight * ($maxWidth / $origWidth));
    $resized = imagecreatetruecolor($newWidth, $newHeight);

    imagealphablending($resized, false);
    imagesavealpha($resized, true);
    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);

    imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
    imagedestroy($img);
    $img = $resized;
}

imagewebp($img, $dest_hero, 85);
imagedestroy($img);
echo "Created $dest_hero\n";
