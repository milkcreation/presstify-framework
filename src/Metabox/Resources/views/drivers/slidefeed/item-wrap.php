<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 */
?>
<li data-control="metabox-slidefeed.items">
    <?php $this->insert('item', $this->get('item')); ?>
</li>