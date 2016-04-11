<?php
class Image{

	public static $useGD = true; // GD native extension

	/**
	*
	* Resize an image with options
	*
	* @param    string  $source_path     Image source path, relative to the script that calls the function
	* @param    string  $dest_path       Image destination path, relative to the script that calls the function
	* @param    string  $width           Expected width (default 0)
	* @param    string  $height          Excpected height (default 0)
	* @param    string  $percent         Excpected percent (overrides width/height, default 0)
	* @param    string  $crop            Cropping (default false)
	* @param    string  $dest_type       Image destination format (default IMAGETYPE_JPEG)
	* @param    string  $jpeg_quality    JPEG Quality (default 90)
	* @param    string  $png_compression PNG Compression (default 0)
	* @return   string  $dest_image      New image path on success, false on error
	*
	*/
	public static function resize($source_path, $dest_path, $width = 0, $height = 0, $percent = 0, $crop = false, $dest_type = IMAGETYPE_JPEG, $jpeg_quality = 90, $png_compression = 0) {

		// Get image dimentions and type and dispatch array values in variables with list
		list($source_width, $source_height, $source_type) = getimagesize($source_path);

		// If percent option is provided and positive value
		if ($percent > 0) {
			$width = $source_width * ($percent / 100);
			$height = $source_height * ($percent / 100);
		} else {

			// Source image is a square
			if ($source_width == $source_height) {
				$ratio = 1;
			} else{

				/*
				if ($source_width > $source_height) {
					// Landscape
				} else {
					// Portrait
				}
				*/

				// Get min/max dimentions in order to get good ratio
				$min_dimension = min($source_width, $source_height);
				$max_dimension = max($source_width, $source_height);
				$ratio = $max_dimension / $min_dimension;
			}

			// If width and height options are not provided, get width/height from source image
			if ($width == 0 && $height == 0) {
				$width = $source_width;
				$height = $source_height;
			// If only width is provided, let's deducing the height maintaining the ratio
			} else if ($height == 0) {
				$height = round($width * $ratio);
			// If only height is provided, let's deducing the width maintaining the ratio
			} else if ($width == 0) {
				$width = round($height * $ratio);
			}
		}

		// Set new width/height for new image dimensions
		$new_width = $width;
		$new_height = $height;
		// Set the offset for centered cropping
		$offsetX = 0;
		$offsetY = 0;

		// If cropping option is active
		if ($crop) {
			// If new image is horizontally smaller than source
			if ($source_width > ($width / $height) * $source_height) {
				$new_height = $height;
				$new_width = round($height * $source_width / $source_height);
				$offsetX = ($new_width - $width) / 2;
				$offsetY = 0;
			}
			// If new image is vertically smaller than source
			if ($source_width < ($width / $height) * $source_height) {
				$new_width = $width;
				$new_height = round($width * $source_height / $source_width);
				$offsetY = ($new_height - $height) / 2;
				$offsetX = 0;
			}
		}

		// Use GD native extension (php.net/GD)
		if(self::$useGD && extension_loaded('GD')) {

			// Make an empty image canvas with new dimensions and 16M+ colors
			$thumb = imagecreatetruecolor($width, $height);

			// Create a temporary image canvas with source image format
			switch ($source_type) {
				case IMAGETYPE_GIF:
					$image = imagecreatefromgif($source_path);
				break;
				case IMAGETYPE_JPEG:
					$image = imagecreatefromjpeg($source_path);
				break;
				case IMAGETYPE_PNG:
					$image = imagecreatefrompng($source_path);
				break;
			}

			// Generate the new image with new dimensions and cropping offsets if active
			imagecopyresampled($thumb, $image, -$offsetX, -$offsetY, 0, 0, $new_width, $new_height, $source_width, $source_height);

			// Split the destination path in array with folder and filename
			$dest_path_infos = pathinfo($dest_path);
			$dest_path = $dest_path_infos['dirname'].'/'.$dest_path_infos['filename'];

			// Add the good format extension to the destination path
			switch ($dest_type) {
				case IMAGETYPE_GIF:
					$dest_path .= '.gif';
					imagegif($thumb, $dest_path);
				break;
				case IMAGETYPE_JPEG:
					$dest_path .= '.jpg';
					// Generate the image with JPEG Quality
					imagejpeg($thumb, $dest_path, $jpeg_quality);
				break;
				case IMAGETYPE_PNG:
					$dest_path .= '.png';
					// Generate the image with PNG Compression
					imagepng($thumb, $dest_path, $png_compression);
				break;
			}

		// Fallback on linux system command if  GD extension not active
		} else {
			// Resize with cropping
			if ($crop) {
				$cmd = '/usr/bin/convert -gravity Center -quality '.$jpeg_quality.' -crop '.$width.'x'.$height.'+0+0 -page '.$width.'x'.$height.' "'.$dest_path.'" "'.$dest_path.'"';
				shell_exec($cmd);

			// Resize only
			} else {
				$cmd = '/usr/bin/convert -resize '.$new_width.'x'.$new_height.' "'.$source.'" "'.$dest_path.'"';
				shell_exec($cmd) ;
			}
		}

		return $dest_path;
	}
}