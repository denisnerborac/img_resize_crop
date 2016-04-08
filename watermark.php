<?php

function watermark($source_path = '', $width = 0, $height = 0, $line_width = 1, $bg_color_rgb = array(0,0,0), $line_color_rgb = array(255,255,255), $dest_path = '', $border = true) {

	list($bg_color_red, $bg_color_green, $bg_color_blue) = $bg_color_rgb;
	list($line_color_red, $line_color_green, $line_color_blue) = $line_color_rgb;

	if (!empty($source_path) && file_exists($source_path)) {

		list($width, $height, $type) = getimagesize($source_path);

		switch ($type) {
			case IMAGETYPE_JPEG:
				$image = imagecreatefromjpeg($source_path);
			break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng($source_path);
			break;
		}
	} else {
		$image = imagecreate($width, $height);
	}

	imagecolorallocate($image, $bg_color_red,$bg_color_green,$bg_color_blue);
	imagesetthickness($image, $line_width);
	$black = imagecolorallocatealpha($image, $line_color_red, $line_color_green, $line_color_blue, 1);

	$x = 0;
	$y = 0;
	$w = $width - 1; // width
	$h = $height - 1; // height

	//borders
	if ($border) {
		imageline($image, $x, $y, $x, $y+$h, $black);
		imageline($image, $x, $y, $x+$w, $y, $black);
		imageline($image, $x+$w, $y, $x+$w, $y+$h, $black);
		imageline($image, $x, $y+$h, $x+$w ,$y+$h, $black);
	}

	//imageline($image, $x1, $y1, $x2, $y2, $color)
	imageline($image, $width, 0, 0, $height, $black);
	imageline($image, 0, 0, $width, $height, $black);
	imageline($image, 0, $height / 2, $width, $height / 2, $black);
	imageline($image, $width / 2, 0, $width / 2, $height, $black);

	if (empty($dest_path)) {
		header('Content-type: image/png');
		imagepng($image);
	} else {
		imagepng($image, $dest_path);
	}
	imagedestroy($image);
}

watermark('img/image.jpeg', 500, 250, 1, array(0,0,0), array(255,255,255), '', false);