<?php
/**
 * @var tiFy\Metabox\MetaboxViewInterface $this
 */

?>
<div <?php echo $this->htmlAttrs(); ?>>
    <ul data-control="metabox-filefeed.items">
        <?php
        foreach ($this->get('items', []) as $item) : ?>
            <?php
            $this->insert('item-wrap', $item); ?>
        <?php
        endforeach; ?>
    </ul>
    <?php
    $this->insert('button', $this->all()); ?>
</div>