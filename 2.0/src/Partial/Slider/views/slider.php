<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<div <?php $this->attrs(); ?>>
    <?php foreach($this->get('items', []) as $item) : ?>
        <?php echo partial('tag', $item); ?>
    <?php endforeach; ?>
</div>
