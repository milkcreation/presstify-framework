<?php
/**
 * @var tiFy\Metabox\MetaboxViewInterface $this
 */
?>
<div class="MetaboxSlidefeed-itemFields">
    <?php foreach ($this->get('fields', []) as $name) : ?>
        <div class="MetaboxSlidefeed-itemField MetaboxSlidefeed-itemField--<?php echo $name; ?>">
            <?php $this->insert("field-{$name}", $this->all()); ?>
        </div>
    <?php endforeach; ?>
</div>