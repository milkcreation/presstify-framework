<?php
/**
 * @var int $height
 * @var float $ratio
 * @var string $url
 * @var int $width
 */
?>
<div style="position:relative;width:100%;height:0;padding-bottom:<?php echo $ratio; ?>%;">
    <iframe style="position:absolute;top:0;left:0;width:100%;height:100%;" width="<?php echo $width; ?>" height="<?php echo $height; ?>" src="<?php echo $src; ?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
</div>