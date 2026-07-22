<?php
$src = 'public/images/pars-lian-logo.png';
$dest = 'public/images/pars-lian-logo.webp';
$dest_hero = 'public/images/pars-lian-logo-hero.webp';

function createOptimizedWebP(string $source, string $destination, int $maxWidth): void {
    $img = imagecreatefrompng($source);
    if (!$img) {
        die("Failed to open image\n");
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
        $img = $resized;
    }
    
    imagewebp($img, $destination, 85);
    unset($img);
}

// Main logo: scaled down to max width 320px
createOptimizedWebP($src, $dest, 320);
echo "Created $dest\n";

// Hero logo (from pars-lian-logo-hero.png): max width 518px (as requested in B1)
$src_hero = 'public/images/pars-lian-logo-hero.png';
if (file_exists($src_hero)) {
    createOptimizedWebP($src_hero, $dest_hero, 518);
    echo "Created $dest_hero\n";
} else {
    // If not exists, use main logo
    createOptimizedWebP($src, $dest_hero, 518);
    echo "Created $dest_hero from main logo\n";
}
