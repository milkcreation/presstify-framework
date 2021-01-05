<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 * @var tiFy\Partial\Drivers\ImageLightbox\ImageLightboxItemInterface $item
 */
?>
<a <?php echo $item->getAttrs(); ?>>
    <?php echo $item->getContent(); ?>
</a>