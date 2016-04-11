<?php
require 'Image.class.php';

$resized_image = Image::resize($img_source = 'img/image.jpeg', $img_dest = 'img/image-thumb', $width = 200, $height = 200, $percent = 0, $crop = true, $format = IMAGETYPE_PNG);

?>
<h1>Resize and Crop image</h1>

<h2>Before</h2>
<img src="<?= $img_source ?>" />
<hr>
<h2>After</h2>
<img src="<?= $resized_image ?>" />