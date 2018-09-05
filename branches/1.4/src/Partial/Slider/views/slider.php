<?php
/**
 * @var tiFy\Partial\PartialViewTemplate $this
 */
?>

<div <?php echo $this->htmlAttrs($this->get('attrs')); ?>>
    <?php foreach($this->get('items', []) as $item) : ?>
        <?php echo \partial('tag', $item); ?>
    <?php endforeach; ?>
</div>
