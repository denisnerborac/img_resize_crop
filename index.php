<?php
require 'Image.class.php';

Image::resize($img_source = 'img/image.jpeg', $img_dest = 'img/image-thumb', $width = 200, $height = 200, $percent = 0, $crop = true, $format = IMAGETYPE_PNG);