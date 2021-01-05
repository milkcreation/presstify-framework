<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 */
?>
<?php $this->before(); ?>
    <div <?php $this->attrs(); ?>>
        <?php foreach ($this->get('items', []) as $item) : ?>
            <?php $this->insert('slider-item', compact('item')); ?>
        <?php endforeach; ?>
    </div>
<?php $this->after();