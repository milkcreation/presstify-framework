<?php
/**
 * @var tiFy\Partial\PartialView $this
 * @var tiFy\Contracts\Partial\AccordionItem $item
 */
?>
<div <?php echo $this->htmlAttrs($item->get('attrs', [])); ?>>
    <div class="PartialAccordion-itemContentInner">
        <?php echo $item->getContent(); ?>
    </div>
</div>
