<?php
$src = 'public/images/pars-lian-logo.png';
$dest = 'public/images/pars-lian-logo.webp';
$dest_mobile = 'public/images/pars-lian-logo-mobile.webp';
$dest_hero = 'public/images/pars-lian-logo-hero.webp';
$dest_hero_mobile = 'public/images/pars-lian-logo-hero-mobile.webp';

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

function createOptimizedWebP(string $source, string $destination, int $maxWidth): void {
    $img = loadImage($source);
    if (!$img) {
        die("Failed to open image: $source\n");
    }
    
    // Preserve transparency
    imagepalettetotruecolor($img);
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $origWidth = imagesx($img);
    $origHeight = imagesy($img);
    
    if ($origWidth > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = (int)($origHeight * ($maxWidth / $origWidth));
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
        
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagedestroy($img);
        $img = $resized;
    }
    
    imagewebp($img, $destination, 85);
    imagedestroy($img);
}

// Main logo: 320px
createOptimizedWebP($src, $dest, 320);
echo "Created $dest\n";

// Mobile main logo: 150px
createOptimizedWebP($src, $dest_mobile, 150);
echo "Created $dest_mobile\n";

// Hero logo
$src_hero = 'public/images/pars-lian-logo-hero.png';
if (file_exists($src_hero)) {
    createOptimizedWebP($src_hero, $dest_hero, 518);
    echo "Created $dest_hero\n";
    createOptimizedWebP($src_hero, $dest_hero_mobile, 320);
    echo "Created $dest_hero_mobile\n";
} else {
    createOptimizedWebP($src, $dest_hero, 518);
    echo "Created $dest_hero from main logo\n";
    createOptimizedWebP($src, $dest_hero_mobile, 320);
    echo "Created $dest_hero_mobile from main logo\n";
}
