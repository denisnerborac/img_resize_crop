<?php
class Image{

	public static $useGD = true;

	public static function resize($source_path, $dest_path, $width = 0, $height = 0, $percent = 0, $crop = false, $dest_type = IMAGETYPE_JPEG, $jpeg_quality = 90, $png_compression = 0) {

		list($source_width, $source_height, $source_type) = getimagesize($source_path);

		if ($percent > 0) {
			$width = $source_width * ($percent / 100);
			$height = $source_height * ($percent / 100);
		} else {
			$min_dimension = min($source_width, $source_height);
			$max_dimension = max($source_width, $source_height);
			$ratio = $max_dimension / $min_dimension;

			if ($width == 0 && $height == 0) {
				$width = $source_width;
				$height = $source_height;
			} else if ($height == 0) {
				$height = round($width * $ratio);
			} else if ($width == 0) {
				$width = round($height * $ratio);
			}
		}

		$new_width = $width;
		$new_height = $height;
		$offsetX = 0;
		$offsetY = 0;

		if ($crop) {
			if ($source_width > ($width / $height) * $source_height) {
				$new_height = $height;
				$new_width = round($height * $source_width / $source_height);
				$offsetX = ($new_width - $width) / 2;
				$offsetY = 0;
			}
			if ($source_width < ($width / $height) * $source_height) {
				$new_width = $width;
				$new_height = round($width * $source_height / $source_width);
				$offsetY = ($new_height - $height) / 2;
				$offsetX = 0;
			}
		}

		if(self::$useGD) {

			$thumb = imagecreatetruecolor($width, $height);
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

			imagecopyresampled($thumb, $image, -$offsetX, -$offsetY, 0, 0, $new_width, $new_height, $source_width, $source_height);
			$dest_path_infos = pathinfo($dest_path);
			$dest_path = $dest_path_infos['dirname'].'/'.$dest_path_infos['filename'];

			switch ($dest_type) {
				case IMAGETYPE_GIF:
				$dest_path .= '.gif';
				imagegif($thumb, $dest_path);
				break;
				case IMAGETYPE_JPEG:
				$dest_path .= '.jpg';
				imagejpeg($thumb, $dest_path, $jpeg_quality);
				break;
				case IMAGETYPE_PNG:
				$dest_path .= '.png';
				imagepng($thumb, $dest_path, $png_compression);
				break;
			}
		} else {
			$cmd = '/usr/bin/convert -resize '.$new_width.'x'.$new_height.' "'.$source.'" "'.$dest_path.'"';
			shell_exec($cmd) ;
			$cmd = '/usr/bin/convert -gravity Center -quality '.$jpeg_quality.' -crop '.$width.'x'.$height.'+0+0 -page '.$width.'x'.$height.' "'.$dest_path.'" "'.$dest_path.'"';
			shell_exec($cmd);
		}


		return true;
	}
}