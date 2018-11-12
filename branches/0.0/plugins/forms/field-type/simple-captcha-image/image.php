<?php
require_once(  '../../../../../../../wp-load.php' );
ob_clean();
ob_start();

/*
if (extension_loaded('gd') && function_exists('gd_info')) {
    echo "PHP GD library is installed on your web server";
}
else {
    echo "PHP GD library is NOT installed on your web server";
}*/

function imageCreateFromAny( $filepath ) {
    $type = exif_imagetype( $filepath );
    $allowedTypes = array(
        1,  // [] gif
        2,  // [] jpg
        3,  // [] png
        6   // [] bmp
    );
    if ( ! in_array( $type, $allowedTypes ) )
        return false;
	
    switch ($type) :
        case 1 :
            $im = imageCreateFromGif($filepath);
        break;
        case 2 :
            $im = imageCreateFromJpeg($filepath);
        break;
        case 3 :
            $im = imageCreateFromPng($filepath);
        break;
        case 6 :
            $im = imageCreateFromBmp($filepath);
        break;
	endswitch;
	   
    return $im; 
}
// Configuration
$src 		= apply_filters( "tify_forms_sci_background_image", "texture.jpg" );
$txt_color 	= apply_filters( "tify_forms_sci_text_color", array( 'red' => 200, 'green' => 200, 'blue' => 200 ) );
// Traitement
$img 		= imageCreateFromAny( $src );
$image_text = empty( $_SESSION['security_number'] ) ? 'error' : $_SESSION['security_number'];
$text_color = imagecolorallocate( $img, $txt_color['red'], $txt_color['green'], $txt_color['blue'] );
$text 		= imagettftext( $img, 16, rand(-10,10), rand(10,30), rand(25,35), $text_color, "fonts/courbd.ttf", $image_text );

header( "Content-type:image/jpeg" );
header( "Content-Disposition:inline ; filename=".basename( $src ) );	
imagejpeg($img);
imagedestroy($img);