<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 * @var tiFy\Partial\Drivers\CurtainMenu\CurtainMenuCollectionInterface $items
 */
?>
<?php $this->before(); ?>
    <div <?php $this->attrs(); ?>>
        <nav class="CurtainMenu-nav">
            <?php $this->insert('items', [
                'items' => $items->getParentItems(null),
                'depth' => 0,
                'parent' => null
            ]); ?>
        </nav>
    </div>
<?php $this->after();