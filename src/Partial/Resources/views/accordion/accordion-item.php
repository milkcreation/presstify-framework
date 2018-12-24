<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>
<div <?php echo $this->htmlAttrs($this->get('attrs', [])); ?>>
    <div class="PartialAccordion-itemContentInner">
        <?php echo $this->get('content', ''); ?>
    </div>
</div>
