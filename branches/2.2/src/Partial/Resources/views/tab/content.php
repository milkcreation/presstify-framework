<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 * @var tiFy\Partial\Drivers\Tab\TabFactoryInterface[] $items
 * @var int $depth
 */
?>
<div class="Tab-content <?php echo 'Tab-content--' . $this->getTabStyle($depth) . " Tab-content--depth{$depth}"; ?> " data-control="tab.content">
    <?php if ($items = $this->get('items', [])) : ?>
        <?php foreach ($items as $item) : ?>
            <div <?php echo $item->getContentAttrs(); ?>>
                <?php if ($children = $item->getChildren()) : ?>
                    <?php $this->insert('nav', ['depth' => $item->getDepth() + 1, 'items' => $children]); ?>
                    <?php $this->insert('content', ['depth' => $item->getDepth() + 1, 'items' => $children]); ?>
                <?php else : ?>
                    <?php $this->insert('content-item', compact('item')); ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>