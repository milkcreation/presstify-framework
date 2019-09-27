<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 * @var tiFy\Wordpress\Contracts\QueryPost $item
 */
?>
<div <?php echo $this->htmlAttrs($this->params('attrs', [])); ?>>
    <div class="MetaboxRelatedPost-suggest">
        <?php echo field('suggest', $this->params('suggest', [])); ?>
    </div>
    <ul id="MetaboxRelatedPost-items--<?php echo $this->params('index', 0); ?>"
        class="MetaboxRelatedPost-items"
        data-control="metabox.related-post.items"
    >
        <?php foreach ($this->params('items', []) as $index => $item) : ?>
           <?php $this->insert('item', compact('index', 'item', 'name')); ?>
        <?php endforeach; ?>
    </ul>
</div>