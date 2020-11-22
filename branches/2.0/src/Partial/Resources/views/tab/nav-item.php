<?php
/**
 * @var tiFy\Partial\Driver\Tab\TabView $this
 * @var tiFy\Contracts\Partial\TabFactory $item
 */
?>
<a <?php echo $item->getNavAttrs(); ?>>
    <?php echo $item->getTitle(); ?>
</a>
