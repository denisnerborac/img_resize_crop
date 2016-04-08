<?php

header('Content-type: image/png');
$png_image = imagecreate(150, 150);

imagecolorallocate($png_image, 0,0,0);
imagesetthickness($png_image, 5);
$black = imagecolorallocatealpha($png_image, 255, 255, 255, 1);

$x = 0;
$y = 0;
$w = imagesx($png_image) - 1;
$z = imagesy($png_image) - 1;

//borders
imageline($png_image, $x, $y, $x, $y+$z, $black);
imageline($png_image, $x, $y, $x+$w, $y, $black);
imageline($png_image, $x+$w, $y, $x+$w, $y+$z, $black);
imageline($png_image, $x, $y+$z, $x+$w ,$y+$z, $black);

//diagonal
imageline($png_image, $x1 = 150, $y1 = 0, $x2 = 0, $y2 = 150, $black);
imageline($png_image, $x1 = 0, $y1 = 0, $x2 = 150, $y2 = 150, $black);
imageline($png_image, $x1 = 0, $y1 = 75, $x2 = 150, $y2 = 75, $black);
imageline($png_image, $x1 = 75, $y1 = 0, $x2 = 75, $y2 = 150, $black);

imagepng($png_image);
imagedestroy($png_image);