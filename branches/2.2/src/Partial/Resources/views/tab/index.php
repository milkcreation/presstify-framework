<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 * @var tiFy\Partial\Drivers\Tab\TabFactoryInterface[] $items
 */
?>
<?php $this->before(); ?>
    <div <?php echo $this->htmlAttrs(); ?>>
        <?php if ($items = $this->get('items', [])) : ?>
            <?php $this->insert('nav', ['depth' => 0] + compact('items')); ?>
            <?php $this->insert('content', ['depth' => 0] + compact('items')); ?>
        <?php endif; ?>
    </div>
<?php $this->after();